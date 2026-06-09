# mike-p.co.uk

Personal site — PHP, markdown journal, static assets. No build step.

## Local dev

```bash
php -S localhost:8000 router.php
```

Open [http://localhost:8000/](http://localhost:8000/).

Scheduled (future-dated) journal posts show locally with a draft badge. **Production** hides them until publish day. To force draft preview on another host, add to `.env.local` in the repo root:

```
JOURNAL_PREVIEW=1
```

## Production

**Production** is the live site at [https://mike-p.co.uk](https://mike-p.co.uk), hosted on IONOS. It is what visitors see after you deploy — not `localhost`.

The homepage **Recent notes** block shows the three newest *published* posts (by `date` in front matter). Future-dated posts do not appear there on production until that date.

## Deploy

Copy `.deploy.env.example` to `.deploy.env` (gitignored) with IONOS SFTP credentials, then:

```bash
python3 scripts/deploy-sftp.py sync
```

Remove stray experimental client logos from production:

```bash
python3 scripts/deploy-sftp.py cleanup-clients
```

## Docs

- [docs/DESIGN.md](docs/DESIGN.md) — design tokens and typography
- [docs/PRE-DEPLOY.md](docs/PRE-DEPLOY.md) — quick checks before CSS deploys
- [docs/CSS-AUDIT.md](docs/CSS-AUDIT.md) — layout/CSS notes
