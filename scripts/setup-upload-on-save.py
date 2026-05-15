#!/usr/bin/env python3
"""Write .vscode/sftp.json from .deploy.env for upload-on-save (SFTP extension)."""

from __future__ import annotations

import json
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parents[1]
ENV_FILE = REPO / ".deploy.env"
ENV_EXAMPLE = REPO / ".deploy.env.example"
SFTP_EXAMPLE = REPO / ".vscode" / "sftp.json.example"
SFTP_OUT = REPO / ".vscode" / "sftp.json"


def parse_env(path: Path) -> dict[str, str]:
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


def main() -> None:
    env = parse_env(ENV_FILE)
    if not env.get("SFTP_HOST"):
        env = {**parse_env(ENV_EXAMPLE), **env}

    password = env.get("SFTP_PASSWORD", "")
    if not password:
        print(
            "Set SFTP_PASSWORD in .deploy.env, then run this again.",
            file=sys.stderr,
        )
        sys.exit(2)

    if not SFTP_EXAMPLE.is_file():
        print(f"Missing {SFTP_EXAMPLE}", file=sys.stderr)
        sys.exit(2)

    config = json.loads(SFTP_EXAMPLE.read_text())
    config["host"] = env.get("SFTP_HOST", config["host"])
    config["port"] = int(env.get("SFTP_PORT", config.get("port", 22)))
    config["username"] = env.get("SFTP_USER", config["username"])
    config["remotePath"] = env.get("SFTP_REMOTE_PATH", config.get("remotePath", "/"))
    config["password"] = password
    config["uploadOnSave"] = True

    SFTP_OUT.parent.mkdir(parents=True, exist_ok=True)
    SFTP_OUT.write_text(json.dumps(config, indent=2) + "\n")
    print(f"Wrote {SFTP_OUT}")
    print("Install extension: SFTP (Natizyskunk) — Cursor should prompt from extensions.json.")
    print("Then save any site file (Cmd+S) to upload it automatically.")


if __name__ == "__main__":
    main()
