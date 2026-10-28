# Deploying to Render with Docker + Neon Postgres

This guide covers everything needed to deploy the student organization system (Laravel 12 + Vite) as a Docker container on [Render](https://render.com), backed by a free [Neon](https://neon.tech) Postgres database.

## Architecture

```
                https://<service>.onrender.com
                            │
                    ┌───────▼────────┐
                    │  Render edge   │  TLS terminated here
                    │  (proxy)       │
                    └───────┬────────┘
                            │ HTTP on $PORT
                    ┌───────▼────────┐
                    │  Docker image  │
                    │  Laravel 12    │  php artisan serve
                    │  Vite bundles  │  (public/build baked in)
                    └───────┬────────┘
                            │ TLS (sslmode=require)
                    ┌───────▼────────┐
                    │  Neon Postgres │  ap-southeast-1
                    │  sessions      │
                    │  cache         │
                    │  queue         │
                    └────────────────┘
```

Everything in `users`, `sessions`, `cache`, and `jobs` lives in Neon, so nothing is lost on deploys or container restarts.

## Files introduced by this setup

| File | Purpose |
| --- | --- |
| `Dockerfile` | Multi-stage build: Vite assets (node) + PHP runtime with `pdo_pgsql`, `bcmath`, `intl`, `zip`, `opcache` |
| `docker/entrypoint.sh` | Container boot: caches → migrations → idempotent seed → serve on `$PORT` |
| `.dockerignore` | Keeps `.env`, `vendor`, `node_modules`, and stale build artifacts out of the image |
| `render.yaml` | Render Blueprint: service definition + environment variables |
| `bootstrap/app.php` | Now trusts all proxies (needed behind Render's edge) |
| `database/seeders/DatabaseSeeder.php` | Seeder is idempotent (safe to re-run on every boot) |

---

## Prerequisites

- Your repo pushed to GitHub (Render builds from git, or from `render.yaml`)
- A free [Render](https://dashboard.render.com/signup) account
- A free [Neon](https://neon.com/signup) account (no credit card required)
- Locally (for prep + verification): PHP ≥ 8.2, Composer, Docker Desktop. Node/npm are only needed if you want to rebuild assets locally (the Docker image builds them itself).

## Security checklist (do this BEFORE the first real deploy)

1. **Rotate the Neon password.** The original password pasted around while setting up this guide should be considered compromised.
   Neon Console → your project → **Roles** → `neondb_owner` → **Reset password**. Copy the new value — you'll paste it into Render only.
2. **Never commit `.env`** or any file containing `APP_KEY` / `DB_PASSWORD`. Both are injected as Render environment variables.
3. **APP_KEY** is generated locally (below) and pasted into Render. It encrypts nothing in this app's data (sessions live in the DB), but it is still required for Laravel to boot in production.

---

## Stage A — Neon database setup

1. Sign up at [neon.com](https://neon.com/signup) and create a project, e.g. `student-org`, region **ap-southeast-1 (Singapore)**.
2. In Neon's dashboard, copy the **connection string**. Two endpoint variants exist:

| Variant | Host looks like | Port | Use |
| --- | --- | --- | --- |
| Direct | `ep-bold-lake-azyipikh.c-3...neon.tech` | 5432 | ✅ This app (Laravel uses prepared statements) |
| Pooler (pgBouncer) | `ep-bold-lake-azyipikh-pooler.c-3...neon.tech` | 6543 | ❌ Do not use — breaks prepared statements ("prepared statement does not exist" errors) |

   If you accidentally copied the pooled string, just remove the `-pooler` suffix from the hostname and keep port 5432.
3. Record these five values (shown as `key = value` from the connection string):

```
DB_HOST     = ep-bold-lake-azyipikh.c-3.ap-southeast-1.aws.neon.tech
DB_PORT     = 5432
DB_DATABASE = neondb
DB_USERNAME = neondb_owner
DB_PASSWORD = (your rotated Neon password)
```

## Stage B — Generate the app key

On your local machine, in the project root:

```bash
php artisan key:generate --show
```

Copy the `base64:...` value. This becomes `APP_KEY` on Render.

## Stage C — Create the service on Render

### Option A: Blueprint (recommended, uses `render.yaml`)

1. Push this branch to GitHub.
2. Render Dashboard → **New** → **Blueprint** → connect the repo.
3. Render reads `render.yaml`. Because several variables use `sync: false`, you'll be prompted to fill in:
   - `APP_KEY` — value from Stage B
   - `APP_URL` — you can leave it empty now, or set `https://<service-name>.onrender.com` once you know the final URL
   - `DB_PASSWORD` — your rotated Neon password
4. Region is already `singapore` (matches Neon). Click **Apply** — the image builds and boots.

### Option B: Manual

1. Render Dashboard → **New** → **Web Service** → connect the repo.
2. Settings:
   - **Runtime**: Docker (auto-detected from the `Dockerfile`)
   - **Region**: Singapore
   - **Plan**: Free
   - **Health Check Path**: `/up`
3. Add the environment variables (same as `render.yaml`):

| Key | Value | Why |
| --- | --- | --- |
| `APP_ENV` | `production` | correct app mode |
| `APP_DEBUG` | `false` | never expose stack traces publicly |
| `APP_KEY` | `base64:...` from Stage B | required to boot |
| `APP_URL` | `https://<service>.onrender.com` | correct URL generation / emails |
| `DB_CONNECTION` | `pgsql` | Postgres |
| `DB_HOST` | `ep-bold-lake-azyipikh.c-3.ap-southeast-1.aws.neon.tech` | — |
| `DB_PORT` | `5432` | direct endpoint |
| `DB_DATABASE` | `neondb` | — |
| `DB_USERNAME` | `neondb_owner` | — |
| `DB_PASSWORD` | rotated Neon password | secret |
| `DB_SSLMODE` | `require` | Neon requires TLS |
| `LOG_CHANNEL` | `stderr` | makes logs visible in Render's log view |
| `SESSION_DRIVER` | `database` | default, works fine |
| `QUEUE_CONNECTION` | `database` | default, works fine |
| `CACHE_STORE` | `database` | default, works fine |

4. **Create Web Service** — first build takes a few minutes (composer + npm inside the image).

## Stage D — Post-deploy

1. When the deploy is **Live**, open `https://<service>.onrender.com/up` — should show a green "OK" page.
2. If you left `APP_URL` empty, add it now (it should be your final public URL) and redeploy.
3. Open the app root, register a real account, and log in.
4. Rotation cleanup: after the app works with the rotated DB password, change the old password in every tool/password manager where it appeared.

---

## What the container does on every boot

`docker/entrypoint.sh` runs, in order:

1. `config:cache` / `route:cache` / `view:cache` — snapshot production config (env vars are read at boot, not per-request)
2. `migrate --force` — applies pending migrations (idempotent; already-run ones are skipped)
3. `db:seed --force` — creates the test user only if it doesn't already exist (idempotent by design, because Render free tier restarts containers after idle spin-downs)
4. `php artisan serve --host=0.0.0.0 --port=$PORT` — binds to Render's port (10000 by default)

Health check: Laravel's built-in `/up` route responds 200 only when the app + database are alive, so Render won't route traffic to a broken deploy.

## Deploying updates

Nothing special — push to the connected branch and Render auto-redeploys (`autoDeploy: true` in `render.yaml`), rebuilding the Docker image and re-running the entrypoint. Migrations you ship are applied automatically on boot. To run one migration manually (e.g. inspect before a risky change), use Render's **Shell** tab or run locally:

```bash
php artisan migrate --status   # against Neon, with the DB_* env vars set locally
```

## Rolling back

Render keeps previous images; Dashboard → your service → **Deployments** → previous deploy → **Rollback**. Note that rollback re-deploys the old image but does **not** revert database migrations.

---

## MySQL → Postgres notes (why the switch just works)

- The app originally ran on MySQL locally. Laravel's schema builder writes portable SQL, so migrations run unchanged on Postgres (verified by running `migrate --force` against Neon — see below).
- Sessions, cache, and queue connections already used the `database` driver, which transparently follows `DB_CONNECTION`.
- **Avoid going forward**: MySQL-only schema features like `->fullText()`, or raw `DB::statement()` with MySQL syntax. Stick to the schema builder and everything stays portable.

## Verifying locally (before deploying)

The same checks I ran while building this setup:

```bash
# 1. Run migrations against Neon from local PHP
#    (PowerShell: env vars override .env because dotenv is immutable)
$env:DB_CONNECTION='pgsql'; $env:DB_HOST='<DB_HOST>'; $env:DB_PORT='5432'
$env:DB_DATABASE='neondb'; $env:DB_USERNAME='neondb_owner'
$env:DB_PASSWORD='<password>'; $env:DB_SSLMODE='require'
php artisan migrate --force

# 2. Build the image
docker build -t student-org-system .

# 3. Run the container with the same env vars (or a small runtime.env file)
docker run -d --name org-app-test -p 18080:10000 --env-file runtime.env student-org-system

# 4. Smoke test
curl http://localhost:18080/up
<ul>curl -I http://localhost:18080/
```

## Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `ViteManifestNotFoundException` | Assets weren't built into the image | The Dockerfile builds them in stage 1; rebuild the image. Locally: `npm run build` |
| 500 on boot, logs say missing `APP_KEY` | Key not set in Render env | Add `APP_KEY`, redeploy |
| `SQLSTATE[08006] ... connection refused` | Wrong host/port, or DB asleep | Use the direct (non-`-pooler`) host, port `5432`; first request may wake Neon (~1 s) |
| `prepared statement "..." does not exist` | Connecting via the pooled endpoint | Switch `DB_HOST` to the direct endpoint (remove `-pooler`), port `5432` |
| App loads but HTTP (not HTTPS) page is blocked / wrong links | `APP_URL` wrong or proxies untrusted | Set `APP_URL` to the real URL; `bootstrap/app.php` already trusts all proxies |
| First render after idle is slow (~30–60 s) | Render free tier spun the service down | Expected; the next cold start warms up again on demand |
| Gone after some inactivity | Same spin-down | Also expected on free tier |
| Logs empty | Wrong log channel | `LOG_CHANNEL=stderr` is set in `render.yaml`; check Render's Logs tab |

## Free tier limits (what to expect)

- **Render free web service**: 512 MB, spins down after ~15 min idle; first request after spin-down waits until the instance boots. Upgrades remove this.
- **Neon free plan** (verified Oct 2026): 1 GB storage per project, 100 CU-hours/month compute (a steady 0.25 CU ≈ always-on at this scale), autoscaling to 2 CU, scale-to-zero when idle, no credit card required.
- Rendering/serving this app fits the free tier comfortably; nothing else uses the container's (ephemeral) disk — uploads, sessions, cache, and queue are all in Neon.
