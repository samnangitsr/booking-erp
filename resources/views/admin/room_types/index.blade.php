@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.room_types'))
@section('pageTitle', __('admin.nav.room_types'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.room_types') }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('admin.room_types.new') }}
    </a>
@endsection

@section('content')
@php
    $statsCards = [
        ['key' => 'total', 'value' => $stats['total'], 'icon' => 'layers', 'tone' => 'primary'],
        ['key' => 'active', 'value' => $stats['active'], 'icon' => 'check2-circle', 'tone' => 'success'],
        ['key' => 'properties_with_room_types', 'value' => $stats['properties_with_room_types'], 'icon' => 'building', 'tone' => 'info'],
        ['key' => 'physical_rooms', 'value' => $stats['physical_rooms'], 'icon' => 'door-open', 'tone' => 'warning'],
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach($statsCards as $card)
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle" style="width:42px;height:42px;">
                        <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase" data-i18n="admin.room_types.stats.{{ $card['key'] }}">{{ __('admin.room_types.stats.'.$card['key']) }}</div>
                        <div class="fw-bold fs-5 mb-0">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.room_types.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</label>
                <select name="property_id" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(request('property_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.common.status">{{ __('admin.common.status') }}</label>
                <select name="status" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>
                            {{ __('admin.properties.status_value.'.$s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <a href="{{ route('admin.room_types.index') }}" class="btn btn-outline-secondary flex-fill">{{ __('admin.common.reset') }}</a>
                <button type="submit" class="btn btn-primary flex-fill">{{ __('admin.common.search') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle js-datatable" data-url="{{ $datatableUrl }}"
               data-columns="{{ json_encode($columns) }}" data-order='[[0,"desc"]]'>
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
