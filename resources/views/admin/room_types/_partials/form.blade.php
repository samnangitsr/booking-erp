@php
    /** @var \App\Models\RoomType $roomType */
@endphp
<form action="{{ $formAction }}" method="POST" id="room-type-form">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white"><strong data-i18n="admin.properties.tabs.basics">{{ __('admin.properties.tabs.basics') }}</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }} <span class="text-danger">*</span></label>
                            <select name="property_id" class="form-select js-tom-select" required>
                                <option value="">—</option>
                                @foreach($options['properties'] as $p)
                                    <option value="{{ $p->id }}" @selected(old('property_id', $roomType->property_id) == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.room_types.code">{{ __('admin.room_types.code') }}</label>
                            <input type="text" name="room_type_code" class="form-control" value="{{ old('room_type_code', $roomType->room_type_code) }}"
                                   placeholder="{{ __('admin.properties.auto_generate') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.common.status">{{ __('admin.common.status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach($options['statuses'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $roomType->status) === $s)>
                                        {{ __('admin.properties.status_value.'.$s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" data-i18n="admin.room_types.name">{{ __('admin.room_types.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $roomType->name) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" data-i18n="admin.room_types.description">{{ __('admin.room_types.description') }}</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $roomType->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white"><strong data-i18n="admin.room_types.capacity">{{ __('admin.room_types.capacity') }}</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.room_types.max_adults">{{ __('admin.room_types.max_adults') }}</label>
                            <input type="number" min="1" name="max_adults" class="form-control" value="{{ old('max_adults', $roomType->max_adults ?? 2) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.room_types.max_children">{{ __('admin.room_types.max_children') }}</label>
                            <input type="number" min="0" name="max_children" class="form-control" value="{{ old('max_children', $roomType->max_children ?? 0) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.room_types.max_occupancy">{{ __('admin.room_types.max_occupancy') }}</label>
                            <input type="number" min="1" name="max_occupancy" class="form-control" value="{{ old('max_occupancy', $roomType->max_occupancy ?? 2) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.room_types.total_rooms">{{ __('admin.room_types.total_rooms') }}</label>
                            <input type="number" min="0" name="total_rooms" class="form-control" value="{{ old('total_rooms', $roomType->total_rooms ?? 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.room_types.room_size">{{ __('admin.room_types.room_size') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="room_size" class="form-control" value="{{ old('room_size', $roomType->room_size) }}">
                                <select name="room_size_unit" class="form-select" style="max-width:90px;">
                                    @foreach($options['size_units'] as $u)
                                        <option value="{{ $u }}" @selected(old('room_size_unit', $roomType->room_size_unit ?? 'sqm') === $u)>{{ $u }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.room_types.base_price">{{ __('admin.room_types.base_price') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="base_price" class="form-control" value="{{ old('base_price', $roomType->base_price) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white"><strong data-i18n="admin.room_types.bed_setup">{{ __('admin.room_types.bed_setup') }}</strong></div>
                <div class="card-body">
                    @if(empty($options['bed_types']) || $options['bed_types']->isEmpty())
                        <p class="text-muted mb-0">{{ __('admin.room_types.no_bed_types') }}</p>
                    @else
                        <div class="row g-2">
                            @foreach($options['bed_types'] as $bt)
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">{{ $bt->name }} <span class="text-muted">(×{{ $bt->capacity }})</span></label>
                                    <input type="number" min="0" name="bed_types[{{ $bt->id }}]" class="form-control form-control-sm"
                                           value="{{ old('bed_types.'.$bt->id, $bedTypeQuantities[$bt->id] ?? 0) }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white"><strong data-i18n="admin.properties.tabs.amenities">{{ __('admin.properties.tabs.amenities') }}</strong></div>
                <div class="card-body" style="max-height:300px;overflow-y:auto;">
                    @foreach($options['amenities'] as $amenity)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="amenities[]"
                                   id="rt-amenity-{{ $amenity->id }}" value="{{ $amenity->id }}"
                                   @checked(in_array($amenity->id, old('amenities', $selectedAmenityIds)))>
                            <label class="form-check-label" for="rt-amenity-{{ $amenity->id }}">
                                @if($amenity->icon)<i class="bi bi-{{ $amenity->icon }} me-1"></i>@endif
                                {{ $amenity->name }}
                            </label>
                        </div>
                    @endforeach
                    @if($options['amenities']->isEmpty())
                        <p class="text-muted small mb-0">{{ __('admin.properties.no_amenities_available') }}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white"><strong data-i18n="admin.properties.tabs.photos">{{ __('admin.properties.tabs.photos') }}</strong></div>
                <div class="card-body">
                    <div id="rt-image-repeater">
                        @foreach($images as $i => $img)
                            @include('admin.properties._partials.image_row', ['index' => $i, 'image' => $img])
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="rt-add-image">
                        <i class="bi bi-plus-lg"></i> <span data-i18n="admin.properties.add_photo">{{ __('admin.properties.add_photo') }}</span>
                    </button>
                </div>
            </div>
            <template id="rt-image-row-template">
                @include('admin.properties._partials.image_row', ['index' => '__INDEX__', 'image' => null])
            </template>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.room_types.index') }}" class="btn btn-light">{{ __('admin.common.cancel') }}</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> {{ __('admin.common.save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const repeater = document.getElementById('rt-image-repeater');
    const template = document.getElementById('rt-image-row-template');
    const addBtn = document.getElementById('rt-add-image');
    let nextIndex = repeater.querySelectorAll('[data-row]').length;
    addBtn.addEventListener('click', () => {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
        const div = document.createElement('div');
        div.innerHTML = html.trim();
        repeater.appendChild(div.firstElementChild);
    });
    repeater.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-remove-row');
        if (btn) btn.closest('[data-row]').remove();
    });
    document.addEventListener('change', (e) => {
        if (e.target.matches('input.js-cover-toggle')) {
            if (e.target.checked) {
                repeater.querySelectorAll('input.js-cover-toggle').forEach(c => {
                    if (c !== e.target) c.checked = false;
                });
            }
        }
    });
    document.addEventListener('input', (e) => {
        if (e.target.matches('input.js-image-url')) {
            const preview = e.target.closest('[data-row]').querySelector('.js-image-preview');
            if (preview) preview.src = e.target.value || 'https://placehold.co/120x80/e9ecef/6c757d?text=...';
        }
    });
});
</script>
@endpush
