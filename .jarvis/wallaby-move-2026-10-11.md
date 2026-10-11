# Wallaby: move, rebrand and redesign (2026-10-11)

Handoff note. Read this first when picking the project up in a new session.

## Naming

- This project was called **kimiyu**. It is now **Wallaby** (wallaby → *wallet*). Always written with a capital W in the UI.
- **kimiyu** (kimiyu.ch, GitHub org `kimiyuch`) is now the studio/umbrella brand. Each product is an Australian animal: Joey, Bilby, Amor, Wallaby.
- Suffix rule in the `kimiyuch` org: **`.app`** = a real native app (Swift/Xcode), **`.web`** = a website or web application.
  - `kimiyuch/joey.app` (macOS app) + `kimiyuch/joey.web` (its site)
  - `kimiyuch/bilby.app` (native app) + `kimiyuch/bilby.web` (its site)
  - `kimiyuch/amor.web` (web app)
  - Wallaby is a Laravel web app, so it is **`wallaby.web`**.
- Production domain: **wallaby.kimiyu.ch** (same pattern as joey/bilby/amor.kimiyu.ch).
- Email stays on the studio domain: `hello@kimiyu.ch` (sender), `privacy@kimiyu.ch` (privacy page).

## Local setup

- Folder: `/Users/marceli.to/Jamon.digital/Webroot/wallaby.web` (was `kimiyu.ch`, then `wallaby.app`).
- Herd: linked and secured as `wallaby.web` → **https://wallaby.web.test** (PHP 8.4).
- `.env`: `APP_NAME=Wallaby`, `APP_URL=https://wallaby.web.test`.
- Demo login: `peter@demo.com` / `password` (also `maria@demo.com`). Do **not** run `php artisan app:setup-demo` casually: it truncates users, spaces and expenses first.
- Sibling repos for reference were cloned to `/tmp/kimiyuch-ref` (gone after a reboot; re-clone with `gh repo clone kimiyuch/<name>`).

## Git

- `origin` → `https://github.com/kimiyuch/wallaby.web.git` (public; renamed from `wallaby.app` on 2026-10-11, GitHub redirects the old URL).
- `old-origin` → `https://github.com/marceli-to/kimiyu.ch.git` (kept for reference).
- All 2026-10-11 work is committed and pushed as `a384459 Rebrand kimiyu to Wallaby and redesign the landing page`. `.claude/settings.local.json` is deliberately left uncommitted (personal, stale paths).

## Done on 2026-10-11

1. **Rebrand kimiyu → Wallaby** everywhere users see it: page titles, nav, footer, privacy page, emails (subjects, body, sign-off), mail header/footer (`resources/views/vendor/mail/html/`), web manifest, README, `APP_NAME`.
2. **App icon + mascot**, in the sibling style (cute flat animal peeking over the app's object, rounded gradient tile): a wallaby holding on to a mint wallet with a gold coin, on a lilac tile.
   - Source: `scripts/make-icon.mjs` (copied from amor.web's approach). Edit the SVG there and run `node scripts/make-icon.mjs`; it renders everything with Playwright (dev dependency) and builds `favicon.ico` with ImageMagick.
   - Outputs: `public/favicon.svg`, `favicon.ico`, `favicon-96x96.png`, `apple-touch-icon.png`, `web-app-manifest-192x192.png` / `-512x512.png` (maskable), `public/mascot.svg` (no tile, used in the UI), `public/mail-logo.png` (PNG for email clients), `docs/icon.png` (README).
3. **Theme** in `resources/css/app.css` (`@theme`, Tailwind 4 since the upgrade below):
   - `lilac` (icon background) = brand: buttons, links, focus, active nav. White on `lilac-600` = 5.3:1.
   - `mint` (wallet) = settled / positive. Use `mint-700`+ for text.
   - `gold` (coin) = highlights, warnings, "Paid for partner" badge.
   - Red stays for destructive actions. Gray stays for secondary text.
4. **Fonts** like the siblings: Inter (self-hosted `resources/fonts/InterVariable.woff2`, copied from joey.web, weights 400–600 only, so no `font-bold`) and Figtree from Bunny Fonts as `font-display` for headings. Emails use Inter/Figtree too.
5. **Recolour of the whole app**: gray-900/black → lilac, green → mint, amber → gold, indigo → lilac, `font-bold` → `font-semibold`, headings → `font-display`. Icon next to the wordmark in the app layout, mascot on login/register.
6. **Landing page redesign** (`resources/views/welcome.blade.php`, built with the `/ui` skill guidelines, modelled on joey.web): hero with mascot + live copy of the expenses screen (`components/landing/app-preview.blade.php`), How it works (3 steps), dark "No awkward math" section with a worked balance example (numbers verified against `BalanceCalculator`), Features, FAQ, closing CTA. New shared `page-nav` and `page-footer` (also used by the privacy page). Footer credit: "Wallaby, made with ❤️ by kimiyu.ch".
   - Added `blade-ui-kit/blade-heroicons` (same as joey.web).
   - Removed the old animated counters (`resources/js/counter.js`) and background orbs.
7. **Copy** repositioned from co-parents to couples and housemates (landing page and `SetupDemo` demo data: "Demo Household", household expenses).

Tests: 74 passed after every step.

## Open / TODO

- [x] **Commit** the 2026-10-11 work (`a384459`, pushed to `origin/main`).
- [x] **GitHub repo name:** renamed to `kimiyuch/wallaby.web`; `origin` and the README clone URL updated.
- [x] **Domain:** wallaby.kimiyu.ch is live with `APP_URL` set (see Production cutover below).
- [x] **Deploy:** `deploy.sh` deploys this repo to Hostpoint.
- [ ] **Old repo:** archive or delete `marceli-to/kimiyu.ch` once the deploy is moved, then `git remote remove old-origin`.
- [x] **README** in the sibling style (centered `docs/icon.png`, name, pun tagline, link to wallaby.kimiyu.ch).
- [x] **Tailwind 4 / Laravel 13 upgrade** (branch `upgrade-laravel-13-tailwind-4`): Laravel 13, Pest 4, PHPUnit 12, Tailwind 4 through `@tailwindcss/vite`, Vite 8, `laravel-vite-plugin` 3 with Inter (local) and Figtree (Bunny) via `@fonts`. Livewire stays on 3 (Livewire 4 is a separate upgrade). Session serialization is now `json`, which signs everyone out once after deploying.
- [x] **`.claude/settings.local.json`:** reset to the committed version (the stale `Webroot/kimiyu.ch` rules are gone).

## Later on 2026-10-11 (branch `upgrade-laravel-13-tailwind-4`, not merged or pushed yet)

- Welcome section on the expenses screen until the partner joins; invite links explain themselves when opened while signed in.
- Bug fixes: month navigation on the 29th–31st, stale edit form, settling future months; recurring expenses now change from the viewed month on, so settled months never change.
- Deleting an account with an active partner anonymises it (`departed_at`) and keeps the expenses; otherwise everything is deleted.
- Backups are kept 90 days (`--days`).

When deploying this branch:
- [ ] `php artisan migrate` (adds `users.departed_at`).
- [ ] Everyone is signed out once (session serialization is now `json`).
- [ ] Check the server cron for `app:backup-database`: drop any `--keep=7`, or it still caps at 7 backups.
- [x] "Start fresh" on the profile page: once the partner has left and all months are settled, the user gets a new space; running expenses carry over, the old space is deleted.

## Production cutover (2026-10-11, evening)

- Live at https://wallaby.kimiyu.ch from `~/www/wallaby.kimiyu.ch` on Hostpoint (PHP 8.4 via `Use php-fpm php84` in `public/.htaccess`). Deploy with `./deploy.sh`.
- Data moved from the old app in `~/www/kimiyu.ch` (5 users, 130 expenses, 3 settlements, 7 backups); `.env` copied with `APP_NAME=Wallaby`, `APP_URL=https://wallaby.kimiyu.ch`, same `APP_KEY`.
- The old app is in maintenance mode and kimiyu.ch 302-redirects to wallaby.kimiyu.ch (temporary, until the studio site). Previous `.htaccess` saved as `public/.htaccess.before-wallaby-20261011-205053`; crontab saved as `~/crontab-before-wallaby-20261011-205053.txt`.
- [x] Cron: the queue worker and `app:backup-database` run from `~/www/wallaby.kimiyu.ch` with `/usr/local/php84/bin/php`.
- [ ] Old repo `marceli-to/kimiyu.ch`: archive, then `git remote remove old-origin`.
