@extends('admin.layouts.admin_layout')

@section('pageHeading', $roomType->name)
@section('pageTitle', $roomType->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.room_types.index') }}">{{ __('admin.nav.room_types') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $roomType->name }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.room_types.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> {{ __('admin.common.back') }}
    </a>
    <a href="{{ route('admin.room_types.edit', $roomType->id) }}" class="btn btn-outline-primary ms-2">
        <i class="bi bi-pencil"></i> {{ __('admin.common.edit') }}
    </a>
@endsection

@section('content')
@php
    $cover = $roomType->images->where('is_cover', true)->first() ?? $roomType->images->first();
    $coverUrl = $cover?->image_path ?: 'https://placehold.co/1200x320/e9ecef/6c757d?text='.urlencode($roomType->name);
@endphp

<div class="card border-0 shadow-sm mb-3 overflow-hidden">
    <div style="background-image: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.55)), url('{{ $coverUrl }}'); height:220px; background-size:cover; background-position:center;" class="position-relative">
        <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
            @include('admin.properties._partials.status_badge', ['type' => 'status', 'status' => $roomType->status])
            <h3 class="mt-2 mb-1">{{ $roomType->name }}</h3>
            <div class="opacity-75">
                <code class="text-white bg-dark-subtle bg-opacity-25 px-1 rounded">{{ $roomType->room_type_code }}</code>
                @if($roomType->property)
                    · <a href="{{ route('admin.properties.show', $roomType->property->id) }}" class="link-light">{{ $roomType->property->name }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    @php
        $capacityCards = [
            ['key' => 'max_adults', 'val' => $roomType->max_adults, 'icon' => 'person-fill', 'tone' => 'primary'],
            ['key' => 'max_children', 'val' => $roomType->max_children, 'icon' => 'person', 'tone' => 'info'],
            ['key' => 'max_occupancy', 'val' => $roomType->max_occupancy, 'icon' => 'people-fill', 'tone' => 'success'],
            ['key' => 'room_size', 'val' => $roomType->room_size ? $roomType->room_size.' '.($roomType->room_size_unit ?? 'sqm') : '—', 'icon' => 'rulers', 'tone' => 'secondary'],
            ['key' => 'base_price', 'val' => number_format((float) $roomType->base_price, 2), 'icon' => 'cash-coin', 'tone' => 'warning'],
            ['key' => 'rooms_count', 'val' => $roomType->rooms->count(), 'icon' => 'door-open', 'tone' => 'dark'],
        ];
    @endphp
    @foreach($capacityCards as $card)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                        <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted" data-i18n="admin.room_types.stats.{{ $card['key'] }}">{{ __('admin.room_types.stats.'.$card['key']) }}</div>
                        <div class="fs-5 fw-bold">{{ $card['val'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong data-i18n="admin.room_types.description">{{ __('admin.room_types.description') }}</strong></div>
            <div class="card-body">
                <p class="text-muted mb-0">{!! nl2br(e($roomType->description ?: __('admin.properties.no_description'))) !!}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong data-i18n="admin.properties.tabs.amenities">{{ __('admin.properties.tabs.amenities') }}</strong></div>
            <div class="card-body">
                @if($roomType->amenities->isEmpty())
                    <p class="text-muted mb-0">{{ __('admin.properties.no_amenities') }}</p>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($roomType->amenities as $a)
                            <span class="badge bg-primary-subtle text-primary-emphasis fs-6 px-3 py-2">
                                @if($a->icon)<i class="bi bi-{{ $a->icon }} me-1"></i>@endif
                                {{ $a->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong data-i18n="admin.properties.rooms">{{ __('admin.properties.rooms') }}</strong>
                <a href="{{ route('admin.rooms.create', ['property_id' => $roomType->property_id, 'room_type_id' => $roomType->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg"></i> {{ __('admin.rooms.new') }}
                </a>
            </div>
            <div class="card-body">
                @if($roomType->rooms->isEmpty())
                    <p class="text-muted mb-0">{{ __('admin.room_types.no_rooms') }}</p>
                @else
                    @foreach($roomType->rooms->groupBy(fn($r) => $r->floor ?: '—') as $floor => $rooms)
                        <div class="mb-3">
                            <div class="text-uppercase small text-muted mb-2">
                                <i class="bi bi-layers"></i> <span data-i18n="admin.rooms.floor">{{ __('admin.rooms.floor') }}</span>: {{ $floor }}
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($rooms as $room)
                                    <a href="{{ route('admin.rooms.show', $room->id) }}"
                                       class="text-decoration-none">
                                        <span class="badge fs-6 px-3 py-2 room-status-{{ $room->status }}">
                                            {{ $room->room_number }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        @foreach($roomStatuses as $st)
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-1">
                                @include('admin.properties._partials.status_badge', ['type' => 'room', 'status' => $st])
                                <span class="fw-semibold ms-2">{{ $roomStatusCounts[$st] ?? 0 }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.room-status-available { background:#d1f7d6; color:#0a4f1d; }
.room-status-occupied { background:#cfe2ff; color:#0a3a8c; }
.room-status-maintenance { background:#fff3cd; color:#664d03; }
.room-status-inactive { background:#e9ecef; color:#495057; }
</style>
@endpush
@endsection
