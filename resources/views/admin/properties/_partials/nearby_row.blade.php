@php
    $np = $place ?? null;
    $idx = $index;
@endphp
<div class="row g-2 align-items-end mb-2 border-bottom pb-2" data-row>
    <input type="hidden" name="nearby_places[{{ $idx }}][id]" value="{{ $np->id ?? '' }}">
    <div class="col-md-4">
        <label class="form-label small mb-1" data-i18n="admin.properties.nearby_name">{{ __('admin.properties.nearby_name') }}</label>
        <input type="text" class="form-control form-control-sm" name="nearby_places[{{ $idx }}][name]" value="{{ $np->name ?? '' }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-1" data-i18n="admin.properties.nearby_type">{{ __('admin.properties.nearby_type') }}</label>
        <input type="text" class="form-control form-control-sm" name="nearby_places[{{ $idx }}][place_type]" value="{{ $np->place_type ?? '' }}"
               placeholder="airport, mall, beach, ...">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-1" data-i18n="admin.properties.nearby_distance">{{ __('admin.properties.nearby_distance') }}</label>
        <input type="number" step="0.1" min="0" class="form-control form-control-sm" name="nearby_places[{{ $idx }}][distance_km]" value="{{ $np->distance_km ?? '' }}">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-1" data-i18n="admin.properties.nearby_description">{{ __('admin.properties.nearby_description') }}</label>
        <input type="text" class="form-control form-control-sm" name="nearby_places[{{ $idx }}][description]" value="{{ $np->description ?? '' }}">
    </div>
    <div class="col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-row">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>
