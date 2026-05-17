@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.rate_plans'))
@section('pageTitle', __('admin.nav.rate_plans'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.rate_plans') }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> <span data-i18n="admin.rate_plans.new">{{ __('admin.rate_plans.new') }}</span>
    </a>
@endsection

@section('content')
@php
    $statsCards = [
        ['key' => 'total', 'value' => $stats['total'], 'icon' => 'cash-coin', 'tone' => 'primary'],
        ['key' => 'active', 'value' => $stats['active'], 'icon' => 'check2-circle', 'tone' => 'success'],
        ['key' => 'properties', 'value' => $stats['properties'], 'icon' => 'building', 'tone' => 'info'],
        ['key' => 'refundable', 'value' => $stats['refundable'], 'icon' => 'arrow-counterclockwise', 'tone' => 'warning'],
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach($statsCards as $card)
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle"
                             style="width:42px;height:42px;">
                            <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase" data-i18n="admin.rate_plans.stats.{{ $card['key'] }}">
                                {{ __('admin.rate_plans.stats.'.$card['key']) }}
                            </div>
                            <div class="fw-bold fs-5 mb-0">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.rate_plans.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</label>
                <select name="property_id" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(request('property_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}</label>
                <select name="room_type_id" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}" @selected(request('room_type_id') == $rt->id)>{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1" data-i18n="admin.rate_plans.meal_plan">{{ __('admin.rate_plans.meal_plan') }}</label>
                <select name="meal_plan" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($mealPlans as $m)
                        <option value="{{ $m }}" @selected(request('meal_plan') === $m)>{{ __('admin.rate_plans.meal_plan_value.'.$m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1" data-i18n="admin.common.status">{{ __('admin.common.status') }}</label>
                <select name="status" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ __('admin.rate_plans.status_value.'.$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i></button>
                <a href="{{ route('admin.rate_plans.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="rate-plans-table" class="table table-striped table-bordered table-hover w-100">
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && window.jQuery.fn.DataTable) {
        window.jQuery('#rate-plans-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json($datatableUrl),
            pagingType: 'simple_numbers',
            columns: @json($columns),
        });
    }
});
</script>
@endpush
