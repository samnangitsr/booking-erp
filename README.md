# Booking ERP Management System

Multi-branch hotel / property booking ERP built on **Laravel 12** with a Blade
admin panel and an Inertia + React public surface.

## Stack

- **Backend** — Laravel 12, PHP 8.3, SQLite (default) / MySQL / PostgreSQL
- **Admin UI** — Blade + Bootstrap 5
- **Public site** — Inertia + React 18 + Vite
- **Data tables** — Yajra Laravel DataTables (server-side, Bootstrap 5 pagination)
- **Notifications** — PHPFlasher (success / error toasts)
- **Dialogs** — SweetAlert2 (delete confirmations)
- **Date pickers** — flatpickr
- **Selects** — Tom Select (single + remote search)
- **i18n** — Khmer / English, switched without page refresh
- **Auth / RBAC** — manual role / permission system (no Spatie packages)

## Features

- 71 tables covering organisations, branches, properties, rooms, rate plans,
  bookings, payments, invoices, refunds, commissions, payouts, promotions,
  coupons, reviews, services (activities / transfers), customers, partners,
  locations, settings, activity logs.
- Multi-branch context: every authenticated user can switch the active branch
  from the header.
- Manual role / permission system with `model_has_roles`, `model_has_permissions`,
  and `role_has_permissions` pivot tables; the `EnsurePermission` middleware
  guards admin routes.
- Khmer / English language toggle: clicking the language flag posts to
  `POST /lang/switch` and the response is applied to all `data-i18n` /
  `data-i18n-placeholder` / `data-i18n-title` nodes without reloading the page.
- Generic CRUD scaffold (`BaseCrudController` + generic Blade partials) renders
  full Yajra DataTable index + Tom Select / flatpickr enabled forms for any
  model that does not have a bespoke view.

## Getting started

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure env
cp .env.example .env
php artisan key:generate

# 3. Create the SQLite database and migrate + seed
touch database/database.sqlite
php artisan migrate:fresh --seed

# 4. Run dev servers
php artisan serve            # http://127.0.0.1:8000
npm run dev                  # Vite hot module reload
```

### Seeded accounts

| Role         | Email                          | Password   |
| ------------ | ------------------------------ | ---------- |
| Super admin  | super@bookingerp.demo          | `password` |
| Admin        | admin@bookingerp.demo          | `password` |
| Manager      | manager@bookingerp.demo        | `password` |
| Staff        | staff@bookingerp.demo          | `password` |

Login at `/admin/login`.

## Project layout

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                  # 70+ controllers (Yajra DataTables + CRUD)
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── BaseCrudController.php   # generic CRUD base
│   │   │   ├── BranchSwitcherController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── PermissionController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RoleController.php
│   │   │   ├── UserController.php
│   │   │   └── ...                 # one controller per module
│   │   └── LocaleController.php    # POST /lang/switch
│   └── Middleware/
│       ├── EnsurePermission.php    # role / permission gate
│       ├── HandleInertiaRequests.php
│       └── SetLocale.php
├── Models/                         # 71 Eloquent models
│   ├── Concerns/
│   │   └── HasRolesAndPermissions.php
│   ├── Permission.php
│   ├── Role.php
│   └── User.php
└── Support/
    └── Translations.php            # flattens lang files for the frontend

database/
├── migrations/
│   └── 2026_05_13_000001_create_booking_erp_management_system_all_tables.php
└── seeders/                        # Roles, Permissions, Company, Branch, Users, etc.

resources/
├── js/
│   ├── admin.js                    # SweetAlert2, flatpickr, Tom Select, DataTables init + i18n
│   └── inertia/                    # React entry point for the public site
├── sass/
│   └── admin.scss
├── lang/
│   ├── en/admin.php
│   └── km/admin.php
└── views/
    └── admin/
        ├── auth/login.blade.php
        ├── dashboard/index.blade.php
        ├── layouts/
        │   ├── admin_layout.blade.php
        │   └── admin_partials/
        │       ├── head.blade.php
        │       ├── header.blade.php
        │       ├── left_sidebar.blade.php
        │       └── scripts.blade.php
        ├── partials/               # generic CRUD partials
        │   ├── _generic_form_fields.blade.php
        │   ├── _row_actions.blade.php
        │   ├── generic_create.blade.php
        │   ├── generic_edit.blade.php
        │   ├── generic_index.blade.php
        │   └── generic_show.blade.php
        ├── users/                  # bespoke views (role assignment)
        └── roles/                  # bespoke views (permission assignment)

routes/
├── admin.php                       # 480+ named admin routes
└── web.php                         # / and /lang/switch
```

## Theme assets

The provided admin Blade layout references an Aspire / Synth theme bundle at
`public/assets/backend/assets/css|js|images/`. These directories are created but
empty — drop the theme bundle into them whenever you want the original look.
The Vite admin bundle (`resources/sass/admin.scss`) ships full Bootstrap 5,
Bootstrap Icons, flatpickr, Tom Select, DataTables, and SweetAlert2 styling so
the admin UI is fully functional even without the theme files.

## Generic CRUD scaffolding

Any controller that extends `App\Http\Controllers\Admin\BaseCrudController` and
sets `$model`, `$routeName`, `$permissionModule`, and `$columns` automatically
gets:

- `GET /<resource>` &mdash; Yajra DataTable JSON when `?datatable=1`, otherwise
  the generic Blade index page.
- `GET /<resource>/create` and `GET /<resource>/{id}/edit` &mdash; generic form
  that introspects the model's fillables / casts and renders flatpickr,
  Tom Select, checkboxes, file inputs, and textareas automatically.
- `POST /<resource>`, `PUT /<resource>/{id}` &mdash; validation falls back to
  `$request->only($fillable)` if `rules()` is not overridden.
- `DELETE /<resource>/{id}` &mdash; protected by a SweetAlert2 confirmation in
  the row action partial.

Bespoke controllers (`UserController`, `RoleController`, etc.) override
`rules()`, `formOptions()`, `store()` / `update()` to handle password hashing,
role syncing, and permission syncing.

## Manual RBAC

```php
$user->assignRole('manager');                 // attach by name or id
$user->syncRoles(['manager', 'staff']);       // replace assignments
$user->hasRole('manager');                    // true / false
$user->hasPermission('bookings.create');      // includes role-derived perms
$user->givePermissionTo('bookings.cancel');   // direct override
```

The `EnsurePermission` middleware is registered as `permission` and is used per
controller via `$this->authorizeAbility('create')`. Super admins always pass.

## Internationalisation

- Translations live in `resources/lang/en/admin.php` and
  `resources/lang/km/admin.php`.
- `App\Support\Translations::current()` is shared with every Blade response
  (`window.__APP__.translations` in `head.blade.php`) and every Inertia
  response (`HandleInertiaRequests`).
- Clicking a `.js-switch-locale[data-locale="km"]` (or `en`) link posts to
  `/lang/switch`, receives the new translation map, and rewrites all
  `data-i18n` / `data-i18n-placeholder` / `data-i18n-title` nodes in-place.
- The `SetLocale` middleware persists the active locale in session and cookie
  so the choice survives across requests.

## Conventions

- Blade pagination uses Bootstrap 5 (`Paginator::useBootstrapFive()` in
  `AppServiceProvider`).
- All forms with a `js-delete-form` class are intercepted on submit and prompt
  the user via SweetAlert2 before proceeding.
- All `<input class="js-flatpickr-date">`, `js-flatpickr-datetime`, and
  `js-flatpickr-time` inputs are wired up automatically (including inside
  Bootstrap modals after `shown.bs.modal`).
- All `<select class="js-tom-select">` elements become Tom Select widgets;
  `js-tom-select-remote` opts into remote `data-url` search.
- All `<table class="js-datatable">[data-url][data-columns]` elements become
  server-side Yajra DataTables with Bootstrap 5 pagination.

## Testing

```bash
composer test     # PHPUnit + Pest
npm run lint      # ESLint
npm run build     # Vite production build
```
