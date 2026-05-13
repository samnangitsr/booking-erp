@include('admin.layouts.admin_partials.head')

<body>

<div class="wrapper">
    @include('admin.layouts.admin_partials.header')

    @include('admin.layouts.admin_partials.left_sidebar')

    <main class="page-content">
        @hasSection('breadcrumb')
            @yield('breadcrumb')
        @else
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">@yield('pageHeading', __('admin.nav.dashboard'))</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            @yield('breadcrumb_items')
                        </ol>
                    </nav>
                </div>
                @hasSection('toolbar')
                    <div class="ms-auto">@yield('toolbar')</div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <div class="overlay nav-toggle-icon"></div>

    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>

    {{-- Theme switcher --}}
    <div class="switcher-body">
        <button class="btn btn-primary btn-switcher shadow-sm" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
                aria-controls="offcanvasScrolling">
            <i class="bi bi-paint-bucket me-0"></i>
        </button>
        <div class="offcanvas offcanvas-end shadow border-start-0 p-2"
             data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title">{{ __('admin.theme.customizer') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <h6 class="mb-0">{{ __('admin.theme.variation') }}</h6>
                <hr>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="LightTheme" value="light" checked>
                    <label class="form-check-label" for="LightTheme">{{ __('admin.theme.light') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="DarkTheme" value="dark">
                    <label class="form-check-label" for="DarkTheme">{{ __('admin.theme.dark') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="SemiDarkTheme" value="semi-dark">
                    <label class="form-check-label" for="SemiDarkTheme">{{ __('admin.theme.semi_dark') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.admin_partials.scripts')
</body>

</html>
