@extends('admin.layouts.admin_layout')

@section('pageHeading', $property->name)
@section('pageTitle', $property->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.properties.index') }}">{{ __('admin.nav.properties') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $property->name }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.properties.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> {{ __('admin.common.back') }}
    </a>
    <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-outline-primary ms-2">
        <i class="bi bi-pencil"></i> {{ __('admin.common.edit') }}
    </a>
@endsection

@section('content')
@php
    $cover = $property->coverImage();
    $coverUrl = $cover?->image_path ?: 'https://placehold.co/1200x400/e9ecef/6c757d?text='.urlencode($property->name);
@endphp

<div class="card border-0 shadow-sm mb-3 overflow-hidden">
    <div class="property-hero position-relative"
         style="background-image: linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.55)), url('{{ $coverUrl }}'); height:260px; background-size:cover; background-position:center;">
        <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
            <div class="d-flex flex-wrap gap-2 mb-2">
                @include('admin.properties._partials.status_badge', ['type' => 'status', 'status' => $property->status])
                @include('admin.properties._partials.status_badge', ['type' => 'approval', 'status' => $property->approval_status])
                @if($property->is_featured)
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-star-fill me-1"></i>{{ __('admin.properties.featured') }}
                    </span>
                @endif
            </div>
            <h2 class="mb-1">{{ $property->name }}
                <span class="ms-2 text-warning">
                    @for($i = 0; $i < (int) floor((float) $property->star_rating); $i++)
                        <i class="bi bi-star-fill"></i>
                    @endfor
                </span>
            </h2>
            <div class="opacity-75">
                <code class="text-white bg-dark-subtle bg-opacity-25 px-1 rounded">{{ $property->property_code }}</code>
                @if($property->propertyType)
                    · {{ $property->propertyType->name }}
                @endif
                @if($property->city || $property->country)
                    · <i class="bi bi-geo-alt"></i>
                    {{ collect([$property->city?->name, $property->country?->name])->filter()->join(', ') }}
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    @php
        $statCards = [
            ['key' => 'room_types', 'val' => $property->room_types_count, 'icon' => 'layers', 'tone' => 'primary'],
            ['key' => 'rooms', 'val' => $property->rooms_count, 'icon' => 'door-open', 'tone' => 'info'],
            ['key' => 'amenities', 'val' => $property->amenities->count(), 'icon' => 'stars', 'tone' => 'warning'],
            ['key' => 'policies', 'val' => $property->policies->count(), 'icon' => 'shield-check', 'tone' => 'secondary'],
            ['key' => 'bookings', 'val' => $property->bookings_count, 'icon' => 'journal-text', 'tone' => 'success'],
        ];
    @endphp
    @foreach($statCards as $card)
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                        <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted" data-i18n="admin.properties.stats.{{ $card['key'] }}">{{ __('admin.properties.stats.'.$card['key']) }}</div>
                        <div class="fs-5 fw-bold">{{ $card['val'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<ul class="nav nav-tabs" id="property-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button">
            <i class="bi bi-info-circle me-1"></i><span data-i18n="admin.properties.tabs.overview">{{ __('admin.properties.tabs.overview') }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-amenities" type="button">
            <i class="bi bi-stars me-1"></i><span data-i18n="admin.properties.tabs.amenities">{{ __('admin.properties.tabs.amenities') }}</span> <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $property->amenities->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rooms" type="button">
            <i class="bi bi-door-open me-1"></i><span data-i18n="admin.properties.tabs.rooms">{{ __('admin.properties.tabs.rooms') }}</span> <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $property->room_types_count }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-photos" type="button">
            <i class="bi bi-images me-1"></i><span data-i18n="admin.properties.tabs.photos">{{ __('admin.properties.tabs.photos') }}</span> <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $property->images->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-policies" type="button">
            <i class="bi bi-shield-check me-1"></i><span data-i18n="admin.properties.tabs.policies">{{ __('admin.properties.tabs.policies') }}</span> <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $property->policies->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-nearby" type="button">
            <i class="bi bi-geo-alt me-1"></i><span data-i18n="admin.properties.tabs.nearby">{{ __('admin.properties.tabs.nearby') }}</span> <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $property->nearbyPlaces->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 bg-white p-4">
    <div class="tab-pane fade show active" id="tab-overview">
        <div class="row g-3">
            <div class="col-md-7">
                <h5 class="mb-2" data-i18n="admin.properties.description">{{ __('admin.properties.description') }}</h5>
                <p class="text-muted">{!! nl2br(e($property->description ?: __('admin.properties.no_description'))) !!}</p>

                <h6 class="mt-4 mb-2" data-i18n="admin.properties.address">{{ __('admin.properties.address') }}</h6>
                <p class="mb-1">{{ $property->address ?: '—' }}</p>
                @if($property->latitude || $property->longitude)
                    <small class="text-muted">{{ $property->latitude }}, {{ $property->longitude }}</small>
                @endif
            </div>
            <div class="col-md-5">
                <div class="card border bg-light">
                    <div class="card-body">
                        <h6 class="card-title" data-i18n="admin.properties.contact">{{ __('admin.properties.contact') }}</h6>
                        <dl class="row mb-0 small">
                            <dt class="col-5" data-i18n="admin.properties.phone">{{ __('admin.properties.phone') }}</dt>
                            <dd class="col-7">{{ $property->phone ?: '—' }}</dd>
                            <dt class="col-5" data-i18n="admin.properties.email">{{ __('admin.properties.email') }}</dt>
                            <dd class="col-7">{{ $property->email ?: '—' }}</dd>
                            <dt class="col-5" data-i18n="admin.properties.check_in_time">{{ __('admin.properties.check_in_time') }}</dt>
                            <dd class="col-7">{{ $property->check_in_time ?: '—' }}</dd>
                            <dt class="col-5" data-i18n="admin.properties.check_out_time">{{ __('admin.properties.check_out_time') }}</dt>
                            <dd class="col-7">{{ $property->check_out_time ?: '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-amenities">
        @if($property->amenities->isEmpty())
            <p class="text-muted mb-0" data-i18n="admin.properties.no_amenities">{{ __('admin.properties.no_amenities') }}</p>
        @else
            <div class="d-flex flex-wrap gap-2">
                @foreach($property->amenities as $a)
                    <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis px-3 py-2 fs-6">
                        @if($a->icon)
                            <i class="bi bi-{{ $a->icon }} me-1"></i>
                        @endif
                        {{ $a->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="tab-pane fade" id="tab-rooms">
        @if($property->roomTypes->isEmpty())
            <p class="text-muted">{{ __('admin.properties.no_room_types') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th data-i18n="admin.room_types.name">{{ __('admin.room_types.name') }}</th>
                            <th data-i18n="admin.room_types.capacity">{{ __('admin.room_types.capacity') }}</th>
                            <th data-i18n="admin.room_types.base_price">{{ __('admin.room_types.base_price') }}</th>
                            <th data-i18n="admin.properties.rooms">{{ __('admin.properties.rooms') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($property->roomTypes as $rt)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.room_types.show', $rt->id) }}" class="fw-semibold">{{ $rt->name }}</a>
                                    <div class="text-muted small">{{ $rt->room_type_code }}</div>
                                </td>
                                <td>
                                    <i class="bi bi-person-fill"></i> {{ $rt->max_adults }} ·
                                    <i class="bi bi-person"></i> {{ $rt->max_children }}
                                </td>
                                <td>{{ number_format((float) $rt->base_price, 2) }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $rt->rooms_count }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.room_types.show', $rt->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h6 class="mt-4 mb-2" data-i18n="admin.properties.room_status_breakdown">{{ __('admin.properties.room_status_breakdown') }}</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach($roomStatuses as $st)
                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                        @include('admin.properties._partials.status_badge', ['type' => 'room', 'status' => $st])
                        <span class="fw-semibold ms-2">{{ $roomStatusCounts[$st] ?? 0 }}</span>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="tab-pane fade" id="tab-photos">
        @if($property->images->isEmpty())
            <p class="text-muted">{{ __('admin.properties.no_photos') }}</p>
        @else
            <div class="row g-3">
                @foreach($property->images as $img)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="{{ $img->image_path }}" alt="{{ $img->title }}" class="card-img-top" style="height:160px;object-fit:cover;">
                            <div class="card-body py-2">
                                <div class="small fw-semibold text-truncate">{{ $img->title ?: __('admin.properties.photo') }}</div>
                                @if($img->is_cover)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>{{ __('admin.properties.cover') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="tab-pane fade" id="tab-policies">
        @if($property->policies->isEmpty())
            <p class="text-muted">{{ __('admin.properties.no_policies') }}</p>
        @else
            <div class="accordion" id="policiesAcc">
                @foreach($property->policies as $i => $policy)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#policy-{{ $policy->id }}">
                                <span class="badge bg-secondary-subtle text-secondary me-2">{{ Str::title(str_replace('_', ' ', $policy->policy_type)) }}</span>
                                <strong>{{ $policy->title }}</strong>
                            </button>
                        </h2>
                        <div id="policy-{{ $policy->id }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#policiesAcc">
                            <div class="accordion-body text-muted">
                                {!! nl2br(e($policy->description ?? '')) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="tab-pane fade" id="tab-nearby">
        @if($property->nearbyPlaces->isEmpty())
            <p class="text-muted">{{ __('admin.properties.no_nearby') }}</p>
        @else
            <ul class="list-group">
                @foreach($property->nearbyPlaces as $place)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $place->name }}</strong>
                            @if($place->place_type)
                                <span class="badge bg-light text-muted ms-2">{{ $place->place_type }}</span>
                            @endif
                            @if($place->description)
                                <div class="text-muted small">{{ $place->description }}</div>
                            @endif
                        </div>
                        @if($place->distance_km !== null)
                            <span class="badge bg-info-subtle text-info-emphasis">
                                <i class="bi bi-geo"></i> {{ number_format((float) $place->distance_km, 1) }} km
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
