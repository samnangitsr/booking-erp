@extends('admin.layouts.admin_layout')

@section('pageHeading', $ratePlan->name)
@section('pageTitle', $ratePlan->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rate_plans.index') }}">{{ __('admin.nav.rate_plans') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $ratePlan->name }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.daily_rates.index', ['property_id' => $ratePlan->property_id]) }}" class="btn btn-outline-primary">
        <i class="bi bi-calendar3"></i> <span data-i18n="admin.daily_rates.open_calendar">{{ __('admin.daily_rates.open_calendar') }}</span>
    </a>
    <a href="{{ route('admin.rate_plans.edit', $ratePlan->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> <span data-i18n="admin.common.edit">{{ __('admin.common.edit') }}</span>
    </a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="mb-1">{{ $ratePlan->name }}</h4>
                        <div class="text-muted small">
                            <code>{{ $ratePlan->rate_plan_code }}</code>
                            ·
                            <a href="{{ route('admin.properties.show', $ratePlan->property_id) }}">{{ $ratePlan->property?->name }}</a>
                            ·
                            <a href="{{ route('admin.room_types.show', $ratePlan->room_type_id) }}">{{ $ratePlan->roomType?->name }}</a>
                        </div>
                    </div>
                    @include('admin.rate_plans._partials.status_badge', ['status' => $ratePlan->status])
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small" data-i18n="admin.rate_plans.meal_plan">{{ __('admin.rate_plans.meal_plan') }}</div>
                        @include('admin.rate_plans._partials.meal_plan_badge', ['value' => $ratePlan->meal_plan])
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small" data-i18n="admin.rate_plans.payment_policy">{{ __('admin.rate_plans.payment_policy') }}</div>
                        <span class="badge bg-light text-dark"
                              data-i18n="admin.rate_plans.payment_policy_value.{{ $ratePlan->payment_policy }}">{{ __('admin.rate_plans.payment_policy_value.'.$ratePlan->payment_policy) }}</span>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small" data-i18n="admin.rate_plans.refundable">{{ __('admin.rate_plans.refundable') }}</div>
                        @if($ratePlan->is_refundable)
                            <span class="badge bg-success-subtle text-success-emphasis"
                                  data-i18n="admin.rate_plans.refundable">{{ __('admin.rate_plans.refundable') }}</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger-emphasis"
                                  data-i18n="admin.rate_plans.non_refundable">{{ __('admin.rate_plans.non_refundable') }}</span>
                        @endif
                    </div>
                    @if($ratePlan->cancellationPolicy)
                        <div class="col-12">
                            <div class="text-muted small" data-i18n="admin.rate_plans.cancellation_policy">{{ __('admin.rate_plans.cancellation_policy') }}</div>
                            <div>{{ $ratePlan->cancellationPolicy->name }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0" data-i18n="admin.rate_plans.next_14_days">{{ __('admin.rate_plans.next_14_days') }}</h5>
                <a href="{{ route('admin.daily_rates.index', ['property_id' => $ratePlan->property_id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-grid-3x3-gap"></i> <span data-i18n="admin.daily_rates.open_calendar">{{ __('admin.daily_rates.open_calendar') }}</span>
                </a>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($previewDates as $row)
                        @php
                            $tone = $row['stop_sell'] ? 'danger' : ($row['base_price'] ? 'primary' : 'secondary');
                        @endphp
                        <div class="border rounded p-2 text-center" style="min-width: 100px;">
                            <div class="small text-muted">{{ $row['date']->format('D') }}</div>
                            <div class="fw-semibold">{{ $row['date']->format('M j') }}</div>
                            <div class="text-{{ $tone }} fw-bold mt-1">
                                @if($row['stop_sell'])
                                    <i class="bi bi-slash-circle" data-i18n="admin.daily_rates.stop_sell" title="{{ __('admin.daily_rates.stop_sell') }}"></i>
                                @elseif($row['base_price'])
                                    ${{ number_format((float) $row['base_price'], 2) }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
                <div class="text-muted small" data-i18n="admin.rate_plans.rates_count">{{ __('admin.rate_plans.rates_count') }}</div>
                <div class="display-4 fw-bold text-primary">{{ $ratePlan->daily_rates_count }}</div>
                <div class="text-muted small" data-i18n="admin.rate_plans.rates_count_hint">{{ __('admin.rate_plans.rates_count_hint') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
