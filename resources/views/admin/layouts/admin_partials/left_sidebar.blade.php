@php
    /** @var \App\Models\User|null $authUser */
    $authUser = auth()->user();
    $can = fn (string $permission) => $authUser?->hasPermission($permission) ?? false;
@endphp
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/backend') }}/images/logo-icon.png" class="logo-icon" alt="logo icon" onerror="this.style.display='none'">
        </div>
        <div>
            <h4 class="logo-text">{{ config('app.name', 'Booking ERP') }}</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class="bi bi-chevron-double-left"></i></div>
    </div>

    <ul class="metismenu" id="menu">

        <li class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class="bi bi-house-door"></i></div>
                <div class="menu-title" data-i18n="admin.nav.dashboard">{{ __('admin.nav.dashboard') }}</div>
            </a>
        </li>

        @if($can('bookings.view'))
        <li class="{{ request()->is('admin/bookings*') ? 'mm-active' : '' }}">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="menu-title" data-i18n="admin.nav.bookings">{{ __('admin.nav.bookings') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.bookings.index') }}"><i class="bi bi-arrow-right-short"></i> <span data-i18n="admin.bookings.list">{{ __('admin.bookings.list') }}</span></a></li>
                @if($can('bookings.create'))
                    <li><a href="{{ route('admin.bookings.create') }}"><i class="bi bi-arrow-right-short"></i> <span data-i18n="admin.bookings.new">{{ __('admin.bookings.new') }}</span></a></li>
                @endif
                <li><a href="{{ route('admin.booking_status_histories.index') }}"><i class="bi bi-arrow-right-short"></i> <span data-i18n="admin.nav.booking_history">{{ __('admin.nav.booking_history') }}</span></a></li>
                <li><a href="{{ route('admin.check_in_out_logs.index') }}"><i class="bi bi-arrow-right-short"></i> <span data-i18n="admin.nav.check_in_out">{{ __('admin.nav.check_in_out') }}</span></a></li>
                <li><a href="{{ route('admin.guests.index') }}"><i class="bi bi-arrow-right-short"></i> <span data-i18n="admin.nav.guests">{{ __('admin.nav.guests') }}</span></a></li>
            </ul>
        </li>
        @endif

        @if($can('properties.view'))
        <li class="{{ request()->is('admin/propert*') || request()->is('admin/room*') || request()->is('admin/rate*') || request()->is('admin/amenities*') ? 'mm-active' : '' }}">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-buildings"></i></div>
                <div class="menu-title" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.properties.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.all_properties') }}</a></li>
                <li><a href="{{ route('admin.property_types.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.property_types') }}</a></li>
                <li><a href="{{ route('admin.room_types.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.room_types') }}</a></li>
                <li><a href="{{ route('admin.rooms.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.rooms') }}</a></li>
                <li><a href="{{ route('admin.bed_types.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.bed_types') }}</a></li>
                <li><a href="{{ route('admin.amenities.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.amenities') }}</a></li>
                <li><a href="{{ route('admin.property_policies.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.property_policies') }}</a></li>
                <li><a href="{{ route('admin.nearby_places.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.nearby_places') }}</a></li>
                <li><a href="{{ route('admin.room_blocks.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.room_blocks') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('rates.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="menu-title" data-i18n="admin.nav.rates_avail">{{ __('admin.nav.rates_avail') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.rate_plans.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.rate_plans') }}</a></li>
                <li><a href="{{ route('admin.daily_rates.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.daily_rates') }}</a></li>
                <li><a href="{{ route('admin.availability_calendars.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.availability') }}</a></li>
                <li><a href="{{ route('admin.occupancy_rules.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.occupancy_rules') }}</a></li>
                <li><a href="{{ route('admin.cancellation_policies.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.cancellation_policies') }}</a></li>
                <li><a href="{{ route('admin.child_age_policies.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.child_age_policies') }}</a></li>
                <li><a href="{{ route('admin.taxes.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.taxes') }}</a></li>
                <li><a href="{{ route('admin.property_fees.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.fees') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('finance.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-wallet2"></i></div>
                <div class="menu-title" data-i18n="admin.nav.finance">{{ __('admin.nav.finance') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.payments.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.payments') }}</a></li>
                <li><a href="{{ route('admin.payment_methods.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.payment_methods') }}</a></li>
                <li><a href="{{ route('admin.invoices.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.invoices') }}</a></li>
                <li><a href="{{ route('admin.refunds.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.refunds') }}</a></li>
                <li><a href="{{ route('admin.commissions.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.commissions') }}</a></li>
                <li><a href="{{ route('admin.payouts.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.payouts') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('marketing.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-megaphone"></i></div>
                <div class="menu-title" data-i18n="admin.nav.marketing">{{ __('admin.nav.marketing') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.promotions.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.promotions') }}</a></li>
                <li><a href="{{ route('admin.coupons.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.coupons') }}</a></li>
                <li><a href="{{ route('admin.reviews.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.reviews') }}</a></li>
                <li><a href="{{ route('admin.wishlists.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.wishlists') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('services.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-suitcase-lg"></i></div>
                <div class="menu-title" data-i18n="admin.nav.services">{{ __('admin.nav.services') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.service_categories.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.service_categories') }}</a></li>
                <li><a href="{{ route('admin.activities.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.activities') }}</a></li>
                <li><a href="{{ route('admin.activity_schedules.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.activity_schedules') }}</a></li>
                <li><a href="{{ route('admin.transfers.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.transfers') }}</a></li>
                <li><a href="{{ route('admin.service_bookings.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.service_bookings') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('customers.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-person-vcard"></i></div>
                <div class="menu-title" data-i18n="admin.nav.customers">{{ __('admin.nav.customers') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.customers.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.all_customers') }}</a></li>
                <li><a href="{{ route('admin.customer_documents.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.customer_documents') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('partners.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-people"></i></div>
                <div class="menu-title" data-i18n="admin.nav.partners">{{ __('admin.nav.partners') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.partners.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.all_partners') }}</a></li>
                <li><a href="{{ route('admin.partner_contracts.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.partner_contracts') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('locations.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-geo-alt"></i></div>
                <div class="menu-title" data-i18n="admin.nav.locations">{{ __('admin.nav.locations') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.countries.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.countries') }}</a></li>
                <li><a href="{{ route('admin.cities.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.cities') }}</a></li>
                <li><a href="{{ route('admin.areas.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.areas') }}</a></li>
                <li><a href="{{ route('admin.destinations.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.destinations') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('organization.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-building-gear"></i></div>
                <div class="menu-title" data-i18n="admin.nav.organization">{{ __('admin.nav.organization') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.companies.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.companies') }}</a></li>
                <li><a href="{{ route('admin.branches.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.branches') }}</a></li>
            </ul>
        </li>
        @endif

        @if($can('users.view') || $can('roles.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-shield-lock"></i></div>
                <div class="menu-title" data-i18n="admin.nav.access_control">{{ __('admin.nav.access_control') }}</div>
            </a>
            <ul>
                @if($can('users.view'))<li><a href="{{ route('admin.users.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.users') }}</a></li>@endif
                @if($can('roles.view'))<li><a href="{{ route('admin.roles.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.roles') }}</a></li>@endif
                @if($can('roles.view'))<li><a href="{{ route('admin.permissions.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.permissions') }}</a></li>@endif
            </ul>
        </li>
        @endif

        @if($can('reports.view') || $can('settings.view'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-gear"></i></div>
                <div class="menu-title" data-i18n="admin.nav.system">{{ __('admin.nav.system') }}</div>
            </a>
            <ul>
                @if($can('reports.view'))<li><a href="{{ route('admin.report_exports.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.report_exports') }}</a></li>@endif
                @if($can('reports.view'))<li><a href="{{ route('admin.activity_logs.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.activity_logs') }}</a></li>@endif
                @if($can('reports.view'))<li><a href="{{ route('admin.login_histories.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.login_histories') }}</a></li>@endif
                @if($can('settings.view'))<li><a href="{{ route('admin.settings.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.settings') }}</a></li>@endif
                @if($can('settings.view'))<li><a href="{{ route('admin.notifications.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.notifications') }}</a></li>@endif
                @if($can('settings.view'))<li><a href="{{ route('admin.notification_templates.index') }}"><i class="bi bi-arrow-right-short"></i> {{ __('admin.nav.notification_templates') }}</a></li>@endif
            </ul>
        </li>
        @endif

    </ul>
</aside>
