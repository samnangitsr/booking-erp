@extends('admin.layouts.admin_layout')

@section('pageHeading', $rate->rate_date->format('M j, Y'))
@section('pageTitle', __('admin.daily_rates.show'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.daily_rates.index') }}">{{ __('admin.nav.daily_rates') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $rate->rate_date->format('Y-m-d') }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.daily_rates.edit', $rate->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> <span data-i18n="admin.common.edit">{{ __('admin.common.edit') }}</span>
    </a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small" data-i18n="admin.daily_rates.date">{{ __('admin.daily_rates.date') }}</div>
                <div class="fw-bold fs-5">{{ $rate->rate_date->format('M j, Y') }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small" data-i18n="admin.nav.rate_plans">{{ __('admin.nav.rate_plans') }}</div>
                <a href="{{ route('admin.rate_plans.show', $rate->rate_plan_id) }}" class="fw-semibold">{{ $rate->ratePlan?->name }}</a>
            </div>
            <div class="col-md-4">
                <div class="text-muted small" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}</div>
                <a href="{{ route('admin.room_types.show', $rate->room_type_id) }}" class="fw-semibold">{{ $rate->roomType?->name }}</a>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.base_price">{{ __('admin.daily_rates.base_price') }}</div>
                <div class="fw-bold text-primary fs-5">{{ $rate->currency_code }} {{ number_format((float) $rate->base_price, 2) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.adult_price">{{ __('admin.daily_rates.adult_price') }}</div>
                <div>{{ number_format((float) $rate->adult_price, 2) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.child_price">{{ __('admin.daily_rates.child_price') }}</div>
                <div>{{ number_format((float) $rate->child_price, 2) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.extra_bed_price">{{ __('admin.daily_rates.extra_bed_price') }}</div>
                <div>{{ number_format((float) $rate->extra_bed_price, 2) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.min_stay">{{ __('admin.daily_rates.min_stay') }}</div>
                <div>{{ $rate->min_stay }} {{ __('admin.daily_rates.nights') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.max_stay">{{ __('admin.daily_rates.max_stay') }}</div>
                <div>{{ $rate->max_stay ?: '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</div>
                <div>{!! $rate->stop_sell ? '<span class="badge bg-danger-subtle text-danger-emphasis">'.__('admin.common.yes').'</span>' : '<span class="badge bg-success-subtle text-success-emphasis">'.__('admin.common.no').'</span>' !!}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small" data-i18n="admin.daily_rates.cta_ctd">{{ __('admin.daily_rates.cta_ctd') }}</div>
                <div>
                    @if($rate->closed_to_arrival) <span class="badge bg-warning-subtle text-warning-emphasis" data-i18n="admin.daily_rates.cta">{{ __('admin.daily_rates.cta') }}</span> @endif
                    @if($rate->closed_to_departure) <span class="badge bg-info-subtle text-info-emphasis" data-i18n="admin.daily_rates.ctd">{{ __('admin.daily_rates.ctd') }}</span> @endif
                    @if(!$rate->closed_to_arrival && !$rate->closed_to_departure) — @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
