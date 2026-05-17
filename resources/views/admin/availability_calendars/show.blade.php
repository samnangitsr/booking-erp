@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.availability'))
@section('pageTitle', $row->available_date->format('M j, Y'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.availability_calendars.index') }}">{{ __('admin.nav.availability') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $row->available_date->format('Y-m-d') }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.availability_calendars.edit', $row->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> <span data-i18n="admin.common.edit">{{ __('admin.common.edit') }}</span>
    </a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="mb-1">{{ $row->roomType?->name ?? '—' }}</h4>
                        <div class="text-muted small">{{ $row->property?->name ?? '—' }}</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $row->available_date->format('D, M j, Y') }}</span>
                </div>
                <div class="row g-3 text-center">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3">
                            <div class="small text-muted text-uppercase" data-i18n="admin.availability.total_rooms">{{ __('admin.availability.total_rooms') }}</div>
                            <div class="fs-3 fw-bold">{{ $row->total_rooms }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 bg-primary-subtle text-primary">
                            <div class="small text-muted text-uppercase" data-i18n="admin.availability.booked_rooms">{{ __('admin.availability.booked_rooms') }}</div>
                            <div class="fs-3 fw-bold">{{ $row->booked_rooms }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 bg-warning-subtle text-warning-emphasis">
                            <div class="small text-muted text-uppercase" data-i18n="admin.availability.blocked_rooms">{{ __('admin.availability.blocked_rooms') }}</div>
                            <div class="fs-3 fw-bold">{{ $row->blocked_rooms }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 bg-success-subtle text-success">
                            <div class="small text-muted text-uppercase" data-i18n="admin.availability.available_rooms">{{ __('admin.availability.available_rooms') }}</div>
                            <div class="fs-3 fw-bold">{{ $row->available_rooms }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-3" data-i18n="admin.common.status">{{ __('admin.common.status') }}</h6>
                @if($row->stop_sell)
                    <span class="badge bg-danger fs-6"><i class="bi bi-slash-circle"></i> <span data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</span></span>
                @else
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> <span data-i18n="admin.availability.open">{{ __('admin.availability.open') }}</span></span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
