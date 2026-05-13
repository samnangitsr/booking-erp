@include('admin.layouts.admin_partials.head')

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="mb-1">{{ config('app.name', 'Booking ERP') }}</h3>
                        <p class="text-muted mb-0">{{ __('admin.auth.sign_in_subtitle') }}</p>
                    </div>

                    <div class="text-end mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary js-switch-locale" data-locale="en">EN</button>
                            <button type="button" class="btn btn-outline-secondary js-switch-locale" data-locale="km">ខ្មែរ</button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.attempt') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label" data-i18n="admin.auth.email">{{ __('admin.auth.email') }}</label>
                            <input id="email" name="email" type="email" class="form-control"
                                   value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label" data-i18n="admin.auth.password">{{ __('admin.auth.password') }}</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>
                        <div class="form-check mb-3">
                            <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
                            <label for="remember" class="form-check-label" data-i18n="admin.auth.remember">{{ __('admin.auth.remember') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" data-i18n="admin.auth.sign_in">{{ __('admin.auth.sign_in') }}</button>
                    </form>
                </div>
            </div>
            <p class="text-muted text-center small mt-3">&copy; {{ date('Y') }} {{ config('app.name', 'Booking ERP') }}</p>
        </div>
    </div>
</div>

@include('admin.layouts.admin_partials.scripts')
</body>
</html>
