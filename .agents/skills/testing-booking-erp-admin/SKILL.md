---
name: testing-booking-erp-admin
description: End-to-end test the Booking ERP Blade admin panel (login, no-refresh i18n, Yajra DataTables, generic CRUD, SweetAlert2 delete, flatpickr, Tom Select). Use when verifying any admin UI change.
---

# Testing the Booking ERP admin panel

This skill covers running the local stack, logging in as the seeded super_admin, and walking the six golden-path admin flows. Verified working as of the initial scaffold (PR #1).

## Devin Secrets Needed

None — the local stack uses a seeded SQLite DB with hard-coded demo passwords. No external services are required for the admin walkthrough.

## Bootstrap the local stack

```bash
cd /home/ubuntu/repos/booking-erp
composer install
npm install
cp .env.example .env  # only if .env doesn't exist
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm run build  # builds Vite assets into public/build/
php artisan serve --host=127.0.0.1 --port=8088
```

Use a separate shell or `&` to background `php artisan serve` so you can keep using the same terminal.

Smoke test the server with `curl -sS -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/admin/login` — expect `200`.

## Seeded credentials

| Email | Password | Role |
|---|---|---|
| `super@bookingerp.demo` | `password` | super_admin (sees everything) |
| `admin@bookingerp.demo` | `password` | admin |
| `manager@bookingerp.demo` | `password` | manager |
| `staff@bookingerp.demo` | `password` | staff |

Seeded branches: Phnom Penh HQ (ID 1), Siem Reap (ID 2), Sihanoukville (ID 3).

## URLs

- Login: `http://127.0.0.1:8088/admin/login`
- Dashboard: `http://127.0.0.1:8088/admin`
- Branches DataTable: `http://127.0.0.1:8088/admin/branches`
- Amenities CRUD (good Tom Select / generic form example): `http://127.0.0.1:8088/admin/amenities`
- Coupons create (good flatpickr example): `http://127.0.0.1:8088/admin/coupons/create`
- Language switch endpoint (AJAX): `POST /lang/switch` with `{ locale: 'km' | 'en' }`

## Six golden-path tests

For recording browser interactions: maximize the window first (`sudo apt-get install -y wmctrl 2>/dev/null; wmctrl -r :ACTIVE: -b add,maximized_vert,maximized_horz`), then start the recording. Annotate each test with `annotate_recording` (`type="test_start"` / `type="assertion"`).

1. **Login → dashboard.** Navigate to `/admin/login`, submit `super@bookingerp.demo` / `password`. Expect redirect to `/admin` with stat tiles and a header containing the branch selector + language switcher + identity badge.

2. **Khmer ↔ English without page reload.** Click the language dropdown, pick ខ្មែរ. The page must NOT navigate (URL stays `/admin`), a single `POST /lang/switch` returns 200 with JSON, and sidebar/dashboard text re-renders to Khmer in-place. Switch back to English to verify reversibility.

3. **Yajra DataTable on /admin/branches.** Should render the 3 seeded branches with Bootstrap 5 pagination. Typing in the search box must fire a `?datatable=1&...&search[value]=…` XHR.

4. **Generic CRUD create.** Properties → Amenities → Add. Fill in name + Tom Select dropdown + status, submit. Expect redirect to `/admin/amenities` with the new row in the DataTable. The PHPFlasher success toast may auto-dismiss before a screenshot can capture it — the row appearing is the more reliable signal.

5. **SweetAlert2 delete confirmation.** Click the trash icon on a row. A `swal2` modal should open with title "Are you sure?" and Yes/Cancel buttons. Click "Yes, delete it!" → row disappears and the total count decrements.

6. **Flatpickr auto-init.** On `/admin/coupons/create` (or any other generic form with a date field), click the Start Date input. A flatpickr calendar should open — NOT the native browser date picker. Look for the month-name dropdown + year input + day-grid pattern (e.g., `aria-label="April 26, 2026"` on cells).

## Known issues / quirks

- The PHPFlasher toast auto-dismisses quickly (~3s); the underlying CRUD operation succeeding (row appears / disappears in the DataTable) is the more reliable signal.
- The `BaseCrudController::datatable()` method uses `DataTables::of($query)`, NOT `DataTables::eloquent()` — the static `eloquent()` is removed in newer versions of `yajra/laravel-datatables-oracle`.
- The browser tab may show `localhost:8088 not secure` warnings in Chrome — this is normal for local HTTP and unrelated to the app.
- The generic CRUD scaffold auto-detects fillables and casts. Form fields without an explicit class won't get Tom Select / flatpickr — verify the relevant `.js-tom-select`, `.js-flatpickr-date`, or `.js-flatpickr-datetime` class is on the rendered `<input>` if the widget isn't initializing.

## File anchors

- Login route: `routes/admin.php` (POST `admin.login.attempt` → `Admin\Auth\LoginController::login`)
- Generic CRUD: `app/Http/Controllers/Admin/BaseCrudController.php` (`datatable()` line ~69; `store()` flashes `admin.common.created`)
- Frontend plugin init: `resources/js/admin.js` (Tom Select, flatpickr, SweetAlert2 delete handler, `applyTranslations()`)
- Language files: `resources/lang/{en,km}/admin.php`
- Layout: `resources/views/admin/layouts/admin_layout.blade.php` + partials in `admin_partials/`
