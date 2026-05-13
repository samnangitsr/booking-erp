@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.dashboard.title'))
@section('pageTitle', __('admin.dashboard.title'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.dashboard.title') }}</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1">{{ __('admin.dashboard.welcome', ['name' => auth()->user()->name ?? '']) }}</h4>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted text-uppercase small" data-i18n="admin.dashboard.today_bookings">{{ __('admin.dashboard.today_bookings') }}</div>
                <div class="h2 mb-0">{{ number_format($stats['today_bookings']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted text-uppercase small" data-i18n="admin.dashboard.total_customers">{{ __('admin.dashboard.total_customers') }}</div>
                <div class="h2 mb-0">{{ number_format($stats['total_customers']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted text-uppercase small" data-i18n="admin.dashboard.total_properties">{{ __('admin.dashboard.total_properties') }}</div>
                <div class="h2 mb-0">{{ number_format($stats['total_properties']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted text-uppercase small" data-i18n="admin.dashboard.total_revenue">{{ __('admin.dashboard.total_revenue') }}</div>
                <div class="h2 mb-0">{{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0" data-i18n="admin.nav.bookings">{{ __('admin.nav.bookings') }}</h5>
    </div>
    <div class="card-body">
        @if($recentBookings->isEmpty())
            <p class="text-muted mb-0" data-i18n="admin.common.no_records">{{ __('admin.common.no_records') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Property</th>
                            <th>Check-in</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $b)
                            <tr>
                                <td>{{ $b->booking_no }}</td>
                                <td>{{ $b->customer?->first_name }} {{ $b->customer?->last_name }}</td>
                                <td>{{ $b->property?->name }}</td>
                                <td>{{ $b->check_in_date?->format('Y-m-d') }}</td>
                                <td>{{ number_format($b->grand_total, 2) }} {{ $b->currency_code }}</td>
                                <td><span class="badge bg-primary">{{ $b->booking_status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
