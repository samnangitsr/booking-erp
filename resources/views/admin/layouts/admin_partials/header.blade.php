@php
  /** @var \App\Models\User|null $authUser */
  $authUser = auth()->user();
  $branches = $authUser
      ? \App\Models\Branch::query()
          ->where(function ($q) use ($authUser) {
              if ($authUser->user_type !== 'super_admin') {
                  $q->where('company_id', $authUser->company_id);
              }
          })
          ->orderBy('name')
          ->limit(20)
          ->get(['id', 'name'])
      : collect();
@endphp
<header class="top-header">
  <nav class="navbar navbar-expand">
    <div class="mobile-toggle-icon d-xl-none">
      <i class="bi bi-list"></i>
    </div>

    <div class="top-navbar d-none d-xl-block">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.dashboard') }}"
            data-i18n="admin.nav.dashboard">{{ __('admin.nav.dashboard') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.bookings.index') }}"
            data-i18n="admin.nav.bookings">{{ __('admin.nav.bookings') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.customers.index') }}"
            data-i18n="admin.nav.customers">{{ __('admin.nav.customers') }}</a>
        </li>
        <li class="nav-item d-none d-xxl-block">
          <a class="nav-link" href="{{ route('admin.properties.index') }}"
            data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</a>
        </li>
        <li class="nav-item d-none d-xxl-block">
          <a class="nav-link" href="{{ route('admin.branches.index') }}"
            data-i18n="admin.nav.branches">{{ __('admin.nav.branches') }}</a>
        </li>
      </ul>
    </div>

    <div class="search-toggle-icon d-xl-none ms-auto">
      <i class="bi bi-search"></i>
    </div>

    <form class="searchbar d-none d-xl-flex ms-auto" role="search" onsubmit="return false;">
      <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
      <input id="globalSearch" class="form-control" type="search" placeholder="{{ __('admin.search_placeholder') }}"
        data-i18n-placeholder="admin.search_placeholder">
      <div class="position-absolute top-50 translate-middle-y d-block d-xl-none search-close-icon"><i
          class="bi bi-x-lg"></i></div>
    </form>

    <div class="top-navbar-right ms-3">
      <ul class="navbar-nav align-items-center">

        {{-- Branch switcher (multi-branch context) --}}
        @if ($authUser && $branches->count() > 0)
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"
              aria-expanded="false">
              <div class="d-flex align-items-center gap-1">
                <i class="bi bi-building"></i>
                <span class="d-none d-md-inline">
                  @php($currentBranchId = session('current_branch_id'))
                  @php($currentBranch = $currentBranchId ? $branches->firstWhere('id', $currentBranchId) : null)
                  {{ $currentBranch?->name ?? __('admin.all_branches') }}
                </span>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <form method="POST" action="{{ route('admin.branch.switch') }}">
                  @csrf
                  <input type="hidden" name="branch_id" value="">
                  <button type="submit" class="dropdown-item"
                    data-i18n="admin.all_branches">{{ __('admin.all_branches') }}</button>
                </form>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              @foreach ($branches as $branch)
                <li>
                  <form method="POST" action="{{ route('admin.branch.switch') }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <button type="submit" class="dropdown-item">{{ $branch->name }}</button>
                  </form>
                </li>
              @endforeach
            </ul>
          </li>
        @endif

        {{-- Language switcher (no refresh) --}}
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"
            aria-expanded="false" id="languageDropdownToggle">
            <div class="d-flex align-items-center gap-1">
              <i class="bi bi-globe2"></i>
              <span class="d-none d-md-inline" id="currentLocaleLabel">
                {{ app()->getLocale() === 'km' ? 'ខ្មែរ' : 'English' }}
              </span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <button type="button" class="dropdown-item js-switch-locale" data-locale="en">English</button>
            </li>
            <li>
              <button type="button" class="dropdown-item js-switch-locale" data-locale="km">ខ្មែរ (Khmer)</button>
            </li>
          </ul>
        </li>

        {{-- User dropdown --}}
        <li class="nav-item dropdown dropdown-large">
          <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"
            aria-expanded="false">
            <div class="user-setting d-flex align-items-center gap-1">
              <img
                src="{{ $authUser?->avatar
                    ? asset('uploads/' . $authUser->avatar)
                    : asset('assets/backend/images/avatars/avatar-1.png') }}"
                class="user-img" alt="">
              <div class="user-name d-none d-sm-block">{{ $authUser?->name ?? 'Guest' }}</div>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                <div class="d-flex align-items-center">
                  <img
                    src="{{ $authUser?->avatar
                        ? asset('uploads/' . $authUser->avatar)
                        : asset('assets/backend/images/avatars/avatar-1.png') }}"
                    alt="" class="rounded-circle" width="60" height="60">
                  <div class="ms-3">
                    <h6 class="mb-0 dropdown-user-name">{{ $authUser?->name ?? 'Guest' }}</h6>
                    <small class="mb-0 dropdown-user-designation text-secondary">
                      {{ ucfirst($authUser?->user_type ?? '') }}
                    </small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                <div class="d-flex align-items-center">
                  <div class="setting-icon"><i class="bi bi-person-fill"></i></div>
                  <div class="setting-text ms-3"><span
                      data-i18n="admin.menu.profile">{{ __('admin.menu.profile') }}</span></div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                <div class="d-flex align-items-center">
                  <div class="setting-icon"><i class="bi bi-gear-fill"></i></div>
                  <div class="setting-text ms-3"><span
                      data-i18n="admin.menu.settings">{{ __('admin.menu.settings') }}</span></div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                <div class="d-flex align-items-center">
                  <div class="setting-icon"><i class="bi bi-speedometer"></i></div>
                  <div class="setting-text ms-3"><span
                      data-i18n="admin.menu.dashboard">{{ __('admin.menu.dashboard') }}</span></div>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item w-100 text-start">
                  <div class="d-flex align-items-center">
                    <div class="setting-icon"><i class="bi bi-box-arrow-right"></i></div>
                    <div class="setting-text ms-3"><span
                        data-i18n="admin.menu.logout">{{ __('admin.menu.logout') }}</span></div>
                  </div>
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
