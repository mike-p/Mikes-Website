#!/usr/bin/env python3
"""Sync site files to IONOS via SFTP. Reads .deploy.env from repo root."""

from __future__ import annotations

import fnmatch
import os
import stat
import sys
from pathlib import Path

try:
    import paramiko
except ImportError:
    print("Install paramiko: pip3 install paramiko", file=sys.stderr)
    sys.exit(1)

REPO = Path(__file__).resolve().parents[1]
ENV_FILE = REPO / ".deploy.env"
ENV_EXAMPLE = REPO / ".deploy.env.example"

SKIP_DIR_NAMES = {".git", "node_modules", ".cursor", ".vscode", ".idea", "scripts"}
SKIP_FILES = {
    "package.json",
    "package-lock.json",
    "playwright.config.js",
    ".DS_Store",
    ".php-cs-fixer.cache",
    ".deploy.env",
    ".deploy.env.example",
}
SKIP_SUFFIXES = {".code-workspace", ".bak"}

# Scratch assets under i/clients/ (sourced during logo work; never deploy)
CLIENT_SCRATCH_PATTERNS = (
    "*-test*.svg",
    "*-temp.svg",
    "*-si.svg",
    "*-vlz.svg",
    "*-icons.svg",
    "*-wordmark.svg",
    "*-full.svg",
)


def _parse_env_file(path: Path) -> dict[str, str]:
    out: dict[str, str] = {}
    if not path.is_file():
        return out
    for line in path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, _, value = line.partition("=")
        value = value.strip().strip("'\"")
        if value:
            out[key.strip()] = value
    return out


def load_env() -> dict[str, str]:
    env = _parse_env_file(ENV_FILE)
    if not env.get("SFTP_HOST"):
        for key, value in _parse_env_file(ENV_EXAMPLE).items():
            env.setdefault(key, value)
    for key in ("SFTP_HOST", "SFTP_USER", "SFTP_PORT", "SFTP_REMOTE_PATH"):
        if key in os.environ:
            env[key] = os.environ[key]
    if os.environ.get("SFTP_PASSWORD"):
        env["SFTP_PASSWORD"] = os.environ["SFTP_PASSWORD"]
    return env


def is_client_scratch(rel: Path) -> bool:
    if len(rel.parts) < 2 or rel.parts[0] != "i" or rel.parts[1] != "clients":
        return False
    return any(fnmatch.fnmatch(rel.name, pattern) for pattern in CLIENT_SCRATCH_PATTERNS)


def skip_path(rel: Path) -> bool:
    if any(p in SKIP_DIR_NAMES for p in rel.parts):
        return True
    if rel.name in SKIP_FILES:
        return True
    if is_client_scratch(rel):
        return True
    return any(rel.name.endswith(s) for s in SKIP_SUFFIXES)


def ensure_remote_dir(sftp: paramiko.SFTPClient, remote: str) -> None:
    remote = remote.rstrip("/") or "/"
    if remote == "/":
        return
    parts = remote.strip("/").split("/")
    path = ""
    for part in parts:
        path += "/" + part
        try:
            sftp.stat(path)
        except OSError:
            sftp.mkdir(path)


def upload_tree(sftp: paramiko.SFTPClient, local: Path, remote_base: str) -> tuple[int, int]:
    uploaded = 0
    skipped = 0
    for item in sorted(local.rglob("*")):
        rel = item.relative_to(local)
        if skip_path(rel):
            skipped += 1
            continue
        remote = f"{remote_base.rstrip('/')}/{rel.as_posix()}"
        if item.is_dir():
            ensure_remote_dir(sftp, remote)
            continue
        ensure_remote_dir(sftp, str(Path(remote).parent))
        sftp.put(str(item), remote)
        uploaded += 1
        print(f"  up {rel.as_posix()}")
    return uploaded, skipped


def cleanup_remote_client_scratch(sftp: paramiko.SFTPClient, remote_base: str) -> int:
    remote_dir = f"{remote_base.rstrip('/')}/i/clients"
    removed = 0
    try:
        entries = sftp.listdir(remote_dir)
    except OSError as exc:
        print(f"Skip remote cleanup ({remote_dir}): {exc}")
        return 0
    for name in entries:
        rel = Path("i") / "clients" / name
        if not is_client_scratch(rel):
            continue
        remote_path = f"{remote_dir}/{name}"
        try:
            sftp.remove(remote_path)
            removed += 1
            print(f"  rm {rel.as_posix()}")
        except OSError as exc:
            print(f"  ! rm {rel.as_posix()}: {exc}", file=sys.stderr)
    return removed


def list_remote(sftp: paramiko.SFTPClient, path: str) -> None:
    print(f"\n{path}:")
    for attr in sorted(sftp.listdir_attr(path), key=lambda a: a.filename):
        kind = "d" if stat.S_ISDIR(attr.st_mode) else "-"
        print(f"  {kind} {attr.filename}")


def connect(env: dict[str, str]) -> paramiko.SFTPClient:
    host = env.get("SFTP_HOST", "")
    user = env.get("SFTP_USER", "")
    port = int(env.get("SFTP_PORT", "22"))
    password = env.get("SFTP_PASSWORD", "")
    key_path = os.path.expanduser(
        env.get("SFTP_IDENTITY_FILE", "~/.ssh/id_ed25519_ionos_mikep")
    )

    transport = paramiko.Transport((host, port))
    pkey = None
    if os.path.isfile(key_path):
        try:
            pkey = paramiko.Ed25519Key.from_private_key_file(key_path)
        except Exception:
            pkey = None

    if pkey is not None:
        try:
            transport.connect(username=user, pkey=pkey)
            print(f"Connected to {host} as {user} (public key)")
            return paramiko.SFTPClient.from_transport(transport)
        except paramiko.AuthenticationException:
            transport.close()
            transport = paramiko.Transport((host, port))

    if not password:
        print(
            "No SFTP password. Set SFTP_PASSWORD=... in .deploy.env (repo root) or export it.",
            file=sys.stderr,
        )
        sys.exit(2)
    transport.connect(username=user, password=password)
    print(f"Connected to {host} as {user} (password)")
    return paramiko.SFTPClient.from_transport(transport)


def main() -> None:
    env = load_env()
    for required in ("SFTP_HOST", "SFTP_USER"):
        if not env.get(required):
            print(
                f"Missing {required}. Add it to .deploy.env or .deploy.env.example",
                file=sys.stderr,
            )
            sys.exit(2)

    remote_base = env.get("SFTP_REMOTE_PATH", "/")
    cmd = sys.argv[1] if len(sys.argv) > 1 else "sync"

    sftp = connect(env)
    try:
        if cmd == "ls":
            list_remote(sftp, remote_base)
        elif cmd == "sync":
            print(f"Syncing {REPO} → {remote_base}")
            n, sk = upload_tree(sftp, REPO, remote_base)
            print(f"Done: {n} files uploaded ({sk} local paths skipped).")
        elif cmd == "cleanup-clients":
            print(f"Removing scratch client logos on {remote_base}")
            n = cleanup_remote_client_scratch(sftp, remote_base)
            print(f"Done: {n} remote file(s) removed.")
        else:
            print("Usage: deploy-sftp.py [ls|sync|cleanup-clients]", file=sys.stderr)
            sys.exit(2)
    finally:
        sftp.close()


if __name__ == "__main__":
    main()
