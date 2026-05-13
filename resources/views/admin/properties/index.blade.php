@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.properties'))
@section('pageTitle', __('admin.nav.properties'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.properties') }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('admin.properties.new') }}
    </a>
@endsection

@section('content')
@php
    $statsCards = [
        ['key' => 'total', 'value' => $stats['total'], 'icon' => 'building', 'tone' => 'primary'],
        ['key' => 'active', 'value' => $stats['active'], 'icon' => 'check2-circle', 'tone' => 'success'],
        ['key' => 'pending_approval', 'value' => $stats['pending_approval'], 'icon' => 'hourglass-split', 'tone' => 'warning'],
        ['key' => 'featured', 'value' => $stats['featured'], 'icon' => 'star-fill', 'tone' => 'info'],
        ['key' => 'total_room_types', 'value' => $stats['total_room_types'], 'icon' => 'layers', 'tone' => 'secondary'],
        ['key' => 'total_rooms', 'value' => $stats['total_rooms'], 'icon' => 'door-open', 'tone' => 'dark'],
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach($statsCards as $card)
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center
                            text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle"
                             style="width:42px;height:42px;">
                            <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase" data-i18n="admin.properties.stats.{{ $card['key'] }}">
                                {{ __('admin.properties.stats.'.$card['key']) }}
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
        <form id="properties-filter" method="GET" action="{{ route('admin.properties.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.common.search">{{ __('admin.common.search') }}</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="{{ __('admin.properties.search_placeholder') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.properties.property_type">{{ __('admin.properties.property_type') }}</label>
                <select name="property_type_id" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($propertyTypes as $pt)
                        <option value="{{ $pt->id }}" @selected(request('property_type_id') == $pt->id)>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2">
                <label class="form-label small mb-1" data-i18n="admin.properties.approval">{{ __('admin.properties.approval') }}</label>
                <select name="approval_status" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($approvalStatuses as $s)
                        <option value="{{ $s }}" @selected(request('approval_status') === $s)>
                            {{ __('admin.properties.approval_status.'.$s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary flex-fill">{{ __('admin.common.reset') }}</a>
                <button type="submit" class="btn btn-primary flex-fill">{{ __('admin.common.search') }}</button>
            </div>
        </form>

        <div class="d-flex justify-content-end mt-3">
            <div class="btn-group btn-group-sm" role="group" aria-label="View toggle" id="property-view-toggle">
                <button type="button" class="btn btn-primary" data-view="cards">
                    <i class="bi bi-grid-3x3-gap"></i> {{ __('admin.properties.view_cards') }}
                </button>
                <button type="button" class="btn btn-outline-primary" data-view="table">
                    <i class="bi bi-table"></i> {{ __('admin.properties.view_table') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="property-cards" class="row g-3">
    @forelse($properties as $property)
        @php
            $cover = $property->images->where('is_cover', true)->first() ?? $property->images->first();
            $coverUrl = $cover?->image_path ?: 'https://placehold.co/640x360/e9ecef/6c757d?text='.urlencode($property->name);
        @endphp
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100 property-card">
                <div class="property-card__cover position-relative" style="background-image:url('{{ $coverUrl }}'); height:180px; background-size:cover; background-position:center;">
                    @if($property->is_featured)
                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                            <i class="bi bi-star-fill me-1"></i>{{ __('admin.properties.featured') }}
                        </span>
                    @endif
                    <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 align-items-end">
                        @include('admin.properties._partials.status_badge', ['type' => 'status', 'status' => $property->status])
                        @include('admin.properties._partials.status_badge', ['type' => 'approval', 'status' => $property->approval_status])
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h5 class="mb-0">
                            <a href="{{ route('admin.properties.show', $property->id) }}" class="text-decoration-none text-dark">
                                {{ $property->name }}
                            </a>
                        </h5>
                        <div class="text-warning small">
                            @for($i = 0; $i < (int) floor((float) $property->star_rating); $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                            @for($i = (int) floor((float) $property->star_rating); $i < 5; $i++)
                                <i class="bi bi-star"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="text-muted small mb-2">
                        <code class="bg-light px-1 rounded">{{ $property->property_code }}</code>
                        @if($property->propertyType)
                            · {{ $property->propertyType->name }}
                        @endif
                    </div>
                    @if($property->city || $property->country)
                        <div class="small text-muted mb-2">
                            <i class="bi bi-geo-alt"></i>
                            {{ collect([$property->city?->name, $property->country?->name])->filter()->join(', ') }}
                        </div>
                    @endif
                    <div class="d-flex gap-3 text-muted small mb-3">
                        <span title="{{ __('admin.nav.room_types') }}">
                            <i class="bi bi-layers"></i> {{ $property->room_types_count }}
                        </span>
                        <span title="{{ __('admin.properties.rooms') }}">
                            <i class="bi bi-door-open"></i> {{ $property->rooms_count }}
                        </span>
                        <span title="{{ __('admin.nav.bookings') }}">
                            <i class="bi bi-journal-text"></i> {{ $property->bookings_count }}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-sm btn-primary flex-fill">
                            <i class="bi bi-eye"></i> {{ __('admin.common.show') }}
                        </a>
                        <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                    <p class="mb-2" data-i18n="admin.properties.empty">{{ __('admin.properties.empty') }}</p>
                    <a href="{{ $createUrl }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> {{ __('admin.properties.new') }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($properties->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $properties->links() }}
    </div>
@endif

<div id="property-table" class="card shadow-sm border-0 mt-3 d-none">
    <div class="card-body">
        @php
            $datatableColumns = $columns;
        @endphp
        <table class="table table-hover align-middle js-datatable" data-url="{{ $datatableUrl }}"
               data-columns="{{ json_encode($datatableColumns) }}" data-order='[[0,"desc"]]'>
            <thead>
                <tr>
                    @foreach($datatableColumns as $col)
                        <th>{{ $col['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('property-view-toggle');
    const cards = document.getElementById('property-cards');
    const table = document.getElementById('property-table');
    if (!toggle) return;
    toggle.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            toggle.querySelectorAll('button').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            const view = btn.dataset.view;
            cards.classList.toggle('d-none', view !== 'cards');
            table.classList.toggle('d-none', view !== 'table');
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.property-card__cover { border-top-left-radius: var(--bs-card-border-radius); border-top-right-radius: var(--bs-card-border-radius); }
.property-card:hover { transform: translateY(-2px); transition: transform 0.18s ease; }
</style>
@endpush
