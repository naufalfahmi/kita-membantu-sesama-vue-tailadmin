**Project Overview**

- **Name:** Kita Membantu Sesama (KMS)
- **Stack:** Laravel (PHP 8.2+, Laravel 12) backend, Vue 3 + TailAdmin frontend (Vite)
- **Frontend location:** `public/admin` (source + build output) — SPA served at `/admin/`
- **Backend location:** main Laravel app (root `app/`, `routes/`, `database/`)

**Purpose of this document**

Berisi ringkasan arsitektur, titik integrasi antara backend dan frontend, instruksi setup dasar, dan panduan file/folder yang relevan untuk analisis (manusia maupun AI). Gunakan ini sebagai starting point untuk memahami codebase dan melakukan analisis lebih lanjut.

---

**Quick facts**

- `composer.json`: Laravel 12, PHP ^8.2, `spatie/laravel-permission` untuk roles/permissions.
- `public/admin`: frontend TailAdmin Vue 3 + TypeScript; `vite.config.ts` base `/admin/`.
- SPA router melakukan fetch ke backend pada endpoint `/admin/api/*` (lihat `src/router/index.ts`).
- Build frontend output: `public/admin/dist` (static files served by Laravel at `/admin/`).

---

**How to run (local development)**

Backend (Laravel):

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure database in .env
php artisan migrate --seed
php artisan serve
```

Frontend (admin SPA):

Option A — develop (hot-reload):

```bash
cd public/admin
npm install
npm run dev
```

Option B — production build (served by Laravel):

```bash
cd public/admin
npm install
npm run build
# build output will be in public/admin/dist and index.html references /admin/assets/*
```

Notes:
- The SPA `index.html` uses asset URLs under `/admin/`, so when you build and copy `dist` into `public/admin/dist`, Laravel can serve the SPA at `/admin`.
- The SPA expects same-origin cookies and fetch credentials `same-origin` for auth checks.

---

**Backend: key locations & responsibilities**

- `app/Http/Controllers/` — controller logic for API endpoints.
- `app/Models/` — Eloquent models (e.g., `User`, `Donatur`, `Transaksi`, `Program`, `Gaji`, `Pangkat`, dsb.).
- `app/Services/` — helper services, e.g., `MenuService.php` used by admin menu API.
- `routes/web.php` — defines frontend routes and the admin API under prefix `admin/api` (both public and authenticated groups). Important routes:
  - `GET /admin/api/csrf-token` — csrf token helper
  - `POST /admin/api/login` — login endpoint
  - `GET /admin/api/user` — authenticated user info
  - `GET /admin/api/menu` — menu items filtered by role/permission
  - Many `Route::apiResource(...)` endpoints for domain resources (donatur, transaksi, program, dll.)
- `database/migrations/` & `database/seeders/` — schema and initial data.

Auth & Authorization:
- `spatie/laravel-permission` is used (roles/permissions).
- `app/Models/User.php` uses `HasRoles` and `SoftDeletes`; look for custom casts and relations.

Where to look first for API logic:
- `app/Http/Controllers/Auth/LoginController.php` — login/user/logout/avatar/password logic referenced by SPA.
- `app/Services/MenuService.php` — builds menu for admin UI according to permissions.
- Controllers referenced in `routes/web.php` like `DonaturController`, `TransaksiController`, `LandingKegiatanController`, `KaryawanController`.

---

**Frontend (public/admin): key locations & responsibilities**

- `src/main.ts` — app entry, registers plugins (Toast, ApexCharts) and mounts Vue app.
- `src/router/index.ts` — full SPA route list and auth-check logic (fetch `/admin/api/user`). Important: `history` base is `/admin/`.
- `src/views/` — main page views organized by feature (Administrasi, Keuangan, Operasional, UserKepegawaian, Company, dsb.). Each view often maps 1:1 to backend resources.
- `src/components/` — shared UI components and layout (e.g., `components/layout/ThemeProvider.vue`, `SidebarProvider.vue`).
- `src/composables/` & `src/utils/` — reusable state and helpers (e.g., `useSidebar`, API helpers).
- `index.html` (in `public/admin`) — includes built assets ` /admin/assets/*` when deployed.

Auth in frontend:
- Router has `checkAuth()` function that calls `/admin/api/user` with `credentials: 'same-origin'` and redirects to `/signin` when unauthenticated.
- Signin view calls `POST /admin/api/login` (see `src/views/Auth/Signin.vue`).

Build & config:
- `vite.config.ts` sets base to `/admin/` so assets and router history match the Laravel route that serves the SPA.
- Dev: `npm run dev` (vite). Build: `npm run build`.

---

**Integration points (where frontend calls backend)**

- Authentication & user: `/admin/api/login`, `/admin/api/logout`, `/admin/api/user`, `/admin/api/csrf-token`.
- Menu: `/admin/api/menu` (returns MenuService::getFilteredMenu())
- Resource APIs: REST endpoints under `/admin/api/<resource>` (many are defined with `Route::apiResource` in `routes/web.php`). Examples:
  - `/admin/api/donatur`
  - `/admin/api/transaksi`
  - `/admin/api/program`
  - `/admin/api/karyawan`

Notes:
- The SPA expects the backend to authenticate using session cookies (Laravel's web middleware), not token-auth by default. Ensure dev server proxying or same-origin runs are configured for local testing.

---

**Suggested checklist for AI-assisted analysis**

1. Backend schema: inspect `database/migrations/*` to learn table shapes and relationships.
2. Models: open `app/Models/*` to map relations and mass-assignable attributes.
3. Controllers: read `app/Http/Controllers/*` for business logic and validation rules.
4. Services: read `app/Services/*` such as `MenuService` for derived data.
5. Routes: `routes/web.php` to enumerate API endpoints and middleware.
6. Frontend routing: `public/admin/src/router/index.ts` to map UI pages to API calls.
7. Views: open `public/admin/src/views/*` corresponding to endpoints you analyze to understand expected JSON shapes and request flows.
8. Network interactions: run the app (or inspect view source) to capture example request/response payloads.

---

**Developer tips / gotchas**

- If you run frontend dev server (`vite`) on a different origin, configure a proxy to `/admin/api` or enable CORS appropriately (backend uses `web` middleware expecting cookies).
- The SPA uses `credentials: 'same-origin'` and expects Laravel session-based auth — don't try token-only flows unless you update both frontend and backend.
- When debugging auth: check `storage/logs/laravel.log` and browser devtools network/cookie behavior.

---

**Where I recommend an AI start analyzing**

- `routes/web.php` — master list of endpoints and how the SPA is mounted.
- `app/Models/User.php` and `app/Services/MenuService.php` — understand auth and menu generation.
- `public/admin/src/router/index.ts` and `public/admin/src/views/Auth/Signin.vue` — understand auth flow from UI.

---

If you ingin saya buat ringkasan yang lebih mendalam (mis. dokumentasi per-endpoint, contoh request/response, ERD dari migrations), beri tahu resources mana yang harus saya prioritaskan dan saya akan generate detailnya.
