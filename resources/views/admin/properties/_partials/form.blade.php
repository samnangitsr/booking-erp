@php
    /** @var \App\Models\Property $property */
    /** @var array $options */
    /** @var \Illuminate\Support\Collection $images */
    /** @var \Illuminate\Support\Collection $policies */
    /** @var \Illuminate\Support\Collection $nearbyPlaces */
    /** @var array $selectedAmenityIds */
    /** @var string $formAction */
    /** @var string $formMethod */
@endphp
<form action="{{ $formAction }}" method="POST" id="property-form">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav nav-pills mb-3" id="property-form-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" type="button" data-bs-toggle="pill" data-bs-target="#tab-basics">
                <i class="bi bi-info-circle me-1"></i><span data-i18n="admin.properties.tabs.basics">{{ __('admin.properties.tabs.basics') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-location">
                <i class="bi bi-geo-alt me-1"></i><span data-i18n="admin.properties.tabs.location">{{ __('admin.properties.tabs.location') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-contact">
                <i class="bi bi-telephone me-1"></i><span data-i18n="admin.properties.tabs.contact">{{ __('admin.properties.tabs.contact') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-amenities-edit">
                <i class="bi bi-stars me-1"></i><span data-i18n="admin.properties.tabs.amenities">{{ __('admin.properties.tabs.amenities') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-photos-edit">
                <i class="bi bi-images me-1"></i><span data-i18n="admin.properties.tabs.photos">{{ __('admin.properties.tabs.photos') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-policies-edit">
                <i class="bi bi-shield-check me-1"></i><span data-i18n="admin.properties.tabs.policies">{{ __('admin.properties.tabs.policies') }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" type="button" data-bs-toggle="pill" data-bs-target="#tab-nearby-edit">
                <i class="bi bi-pin-map me-1"></i><span data-i18n="admin.properties.tabs.nearby">{{ __('admin.properties.tabs.nearby') }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- BASICS --}}
        <div class="tab-pane fade show active" id="tab-basics">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" data-i18n="admin.properties.name">{{ __('admin.properties.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $property->name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.code">{{ __('admin.properties.code') }}</label>
                            <input type="text" name="property_code" class="form-control" value="{{ old('property_code', $property->property_code) }}"
                                   placeholder="{{ __('admin.properties.auto_generate') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.properties.property_type">{{ __('admin.properties.property_type') }}</label>
                            <select name="property_type_id" class="form-select js-tom-select">
                                <option value="">—</option>
                                @foreach($options['property_types'] as $pt)
                                    <option value="{{ $pt->id }}" @selected(old('property_type_id', $property->property_type_id) == $pt->id)>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.properties.stars">{{ __('admin.properties.stars') }}</label>
                            <select name="star_rating" class="form-select">
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected(old('star_rating', (int) $property->star_rating) === $i)>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $property->is_featured))>
                                <label class="form-check-label" for="is_featured" data-i18n="admin.properties.featured">{{ __('admin.properties.featured') }}</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.common.status">{{ __('admin.common.status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach($options['statuses'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $property->status) === $s)>
                                        {{ __('admin.properties.status_value.'.$s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.approval">{{ __('admin.properties.approval') }}</label>
                            <select name="approval_status" class="form-select" required>
                                @foreach($options['approval_statuses'] as $s)
                                    <option value="{{ $s }}" @selected(old('approval_status', $property->approval_status) === $s)>
                                        {{ __('admin.properties.approval_status.'.$s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" data-i18n="admin.properties.description">{{ __('admin.properties.description') }}</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description', $property->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LOCATION --}}
        <div class="tab-pane fade" id="tab-location">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.country">{{ __('admin.properties.country') }}</label>
                            <select name="country_id" class="form-select js-tom-select">
                                <option value="">—</option>
                                @foreach($options['countries'] as $c)
                                    <option value="{{ $c->id }}" @selected(old('country_id', $property->country_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.city">{{ __('admin.properties.city') }}</label>
                            <select name="city_id" class="form-select js-tom-select">
                                <option value="">—</option>
                                @foreach($options['cities'] as $c)
                                    <option value="{{ $c->id }}" @selected(old('city_id', $property->city_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.area">{{ __('admin.properties.area') }}</label>
                            <select name="area_id" class="form-select js-tom-select">
                                <option value="">—</option>
                                @foreach($options['areas'] as $a)
                                    <option value="{{ $a->id }}" @selected(old('area_id', $property->area_id) == $a->id)>{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" data-i18n="admin.properties.address">{{ __('admin.properties.address') }}</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $property->address) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.properties.latitude">{{ __('admin.properties.latitude') }}</label>
                            <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $property->latitude) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.properties.longitude">{{ __('admin.properties.longitude') }}</label>
                            <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $property->longitude) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTACT --}}
        <div class="tab-pane fade" id="tab-contact">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.properties.phone">{{ __('admin.properties.phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $property->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.properties.email">{{ __('admin.properties.email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $property->email) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.check_in_time">{{ __('admin.properties.check_in_time') }}</label>
                            <input type="time" name="check_in_time" class="form-control" value="{{ old('check_in_time', $property->check_in_time) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.check_out_time">{{ __('admin.properties.check_out_time') }}</label>
                            <input type="time" name="check_out_time" class="form-control" value="{{ old('check_out_time', $property->check_out_time) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.properties.min_check_in_age">{{ __('admin.properties.min_check_in_age') }}</label>
                            <input type="number" min="0" max="99" name="min_check_in_age" class="form-control" value="{{ old('min_check_in_age', $property->min_check_in_age) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- AMENITIES --}}
        <div class="tab-pane fade" id="tab-amenities-edit">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-3" data-i18n="admin.properties.amenities_help">{{ __('admin.properties.amenities_help') }}</p>
                    <div class="row g-2">
                        @foreach($options['amenities'] as $amenity)
                            <div class="col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]"
                                           id="amenity-{{ $amenity->id }}" value="{{ $amenity->id }}"
                                           @checked(in_array($amenity->id, old('amenities', $selectedAmenityIds)))>
                                    <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                        @if($amenity->icon)
                                            <i class="bi bi-{{ $amenity->icon }} me-1"></i>
                                        @endif
                                        {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($options['amenities']->isEmpty())
                        <p class="text-muted">{{ __('admin.properties.no_amenities_available') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- PHOTOS --}}
        <div class="tab-pane fade" id="tab-photos-edit">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-3" data-i18n="admin.properties.photos_help">{{ __('admin.properties.photos_help') }}</p>
                    <div id="image-repeater">
                        @foreach($images as $i => $img)
                            @include('admin.properties._partials.image_row', ['index' => $i, 'image' => $img])
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-image">
                        <i class="bi bi-plus-lg"></i> <span data-i18n="admin.properties.add_photo">{{ __('admin.properties.add_photo') }}</span>
                    </button>
                </div>
            </div>
            <template id="image-row-template">
                @include('admin.properties._partials.image_row', ['index' => '__INDEX__', 'image' => null])
            </template>
        </div>

        {{-- POLICIES --}}
        <div class="tab-pane fade" id="tab-policies-edit">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="policy-repeater">
                        @foreach($policies as $i => $p)
                            @include('admin.properties._partials.policy_row', ['index' => $i, 'policy' => $p, 'policyTypes' => $options['policy_types']])
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-policy">
                        <i class="bi bi-plus-lg"></i> <span data-i18n="admin.properties.add_policy">{{ __('admin.properties.add_policy') }}</span>
                    </button>
                </div>
            </div>
            <template id="policy-row-template">
                @include('admin.properties._partials.policy_row', ['index' => '__INDEX__', 'policy' => null, 'policyTypes' => $options['policy_types']])
            </template>
        </div>

        {{-- NEARBY --}}
        <div class="tab-pane fade" id="tab-nearby-edit">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="nearby-repeater">
                        @foreach($nearbyPlaces as $i => $np)
                            @include('admin.properties._partials.nearby_row', ['index' => $i, 'place' => $np])
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-nearby">
                        <i class="bi bi-plus-lg"></i> <span data-i18n="admin.properties.add_nearby">{{ __('admin.properties.add_nearby') }}</span>
                    </button>
                </div>
            </div>
            <template id="nearby-row-template">
                @include('admin.properties._partials.nearby_row', ['index' => '__INDEX__', 'place' => null])
            </template>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.properties.index') }}" class="btn btn-light">{{ __('admin.common.cancel') }}</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> {{ __('admin.common.save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupRepeater(repeaterId, templateId, addBtnId) {
        const repeater = document.getElementById(repeaterId);
        const template = document.getElementById(templateId);
        const addBtn = document.getElementById(addBtnId);
        if (!repeater || !template || !addBtn) return;

        let nextIndex = repeater.querySelectorAll('[data-row]').length;

        addBtn.addEventListener('click', () => {
            const html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
            const div = document.createElement('div');
            div.innerHTML = html.trim();
            repeater.appendChild(div.firstElementChild);
        });

        repeater.addEventListener('click', (e) => {
            const btn = e.target.closest('.js-remove-row');
            if (btn) {
                btn.closest('[data-row]').remove();
            }
        });
    }

    setupRepeater('image-repeater', 'image-row-template', 'add-image');
    setupRepeater('policy-repeater', 'policy-row-template', 'add-policy');
    setupRepeater('nearby-repeater', 'nearby-row-template', 'add-nearby');

    // Single-cover toggle: ensure only one image is marked as cover
    document.addEventListener('change', (e) => {
        if (e.target.matches('input.js-cover-toggle')) {
            if (e.target.checked) {
                document.querySelectorAll('input.js-cover-toggle').forEach(c => {
                    if (c !== e.target) c.checked = false;
                });
            }
        }
    });

    // Image preview
    document.addEventListener('input', (e) => {
        if (e.target.matches('input.js-image-url')) {
            const preview = e.target.closest('[data-row]').querySelector('.js-image-preview');
            if (preview) preview.src = e.target.value || 'https://placehold.co/120x80/e9ecef/6c757d?text=...';
        }
    });
});
</script>
@endpush
