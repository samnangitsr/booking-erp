<!doctype html>
<html lang="{{ app()->getLocale() }}" class="minimal-theme" data-locale="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend') }}/images/favicon-32x32.png" type="image/png" />

    {{-- Bootstrap 5 (via Vite for hashed assets, plus CDN fallback for the legacy theme look) --}}
    @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])

    {{-- Plugins shipped with the backend theme bundle --}}
    <link href="{{ asset('assets/backend') }}/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />

    {{-- Bootstrap CSS shipped with the theme bundle --}}
    <link href="{{ asset('assets/backend') }}/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/style.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/bootstrap-icons/font/bootstrap-icons.css') }}">

    {{-- Loader --}}
    <link href="{{ asset('assets/backend') }}/css/pace.min.css" rel="stylesheet" />

    {{-- Theme styles --}}
    <link href="{{ asset('assets/backend') }}/css/dark-theme.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/light-theme.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/semi-dark.css" rel="stylesheet" />
    <link href="{{ asset('assets/backend') }}/css/header-colors.css" rel="stylesheet" />

    <title>@yield('pageTitle', __('admin.app_name'))</title>

    <script>
        window.__APP__ = {
            locale: @json(app()->getLocale()),
            fallbackLocale: @json(config('app.fallback_locale', 'en')),
            csrfToken: @json(csrf_token()),
            translations: @json(\App\Support\Translations::current()),
            switchLocaleUrl: @json(route('lang.switch')),
            routes: {
                logout: @json(route('admin.logout')),
            },
        };
    </script>

    {{-- PHPFlasher renders its own assets via @flasher_render in the footer --}}

    @stack('styles')
</head>
