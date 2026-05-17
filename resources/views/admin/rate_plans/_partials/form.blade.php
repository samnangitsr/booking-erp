@php
    $isEdit = $formMethod === 'PUT';
@endphp

<form method="POST" action="{{ $formAction }}" class="row g-3">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0" data-i18n="admin.properties.tabs.basics">{{ __('admin.properties.tabs.basics') }}</h5>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }} <span class="text-danger">*</span></label>
                    <select name="property_id" id="rate_plan_property_id" class="form-select js-tom-select" required>
                        <option value="">{{ __('admin.common.all') }}</option>
                        @foreach($options['properties'] as $p)
                            <option value="{{ $p->id }}" @selected(old('property_id', $ratePlan->property_id) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }} <span class="text-danger">*</span></label>
                    <select name="room_type_id" id="rate_plan_room_type_id" class="form-select js-tom-select" required>
                        <option value="">{{ __('admin.common.all') }}</option>
                        @foreach($options['room_types'] as $rt)
                            <option value="{{ $rt->id }}"
                                    data-property="{{ $rt->property_id }}"
                                    @selected(old('room_type_id', $ratePlan->room_type_id) == $rt->id)>
                                {{ $rt->name }} <small class="text-muted">— {{ $rt->property?->name }}</small>
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.rate_plans.name">{{ __('admin.rate_plans.name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $ratePlan->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.rate_plans.code">{{ __('admin.rate_plans.code') }}</label>
                    <input type="text" name="rate_plan_code" value="{{ old('rate_plan_code', $ratePlan->rate_plan_code) }}" class="form-control"
                           placeholder="{{ __('admin.rate_plans.auto_generate') }}">
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0" data-i18n="admin.rate_plans.inventory">{{ __('admin.rate_plans.inventory') }}</h5>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.rate_plans.meal_plan">{{ __('admin.rate_plans.meal_plan') }}</label>
                    <select name="meal_plan" class="form-select js-tom-select">
                        @foreach($options['meal_plans'] as $m)
                            <option value="{{ $m }}" @selected(old('meal_plan', $ratePlan->meal_plan) === $m)>{{ __('admin.rate_plans.meal_plan_value.'.$m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.rate_plans.payment_policy">{{ __('admin.rate_plans.payment_policy') }}</label>
                    <select name="payment_policy" class="form-select js-tom-select">
                        @foreach($options['payment_policies'] as $p)
                            <option value="{{ $p }}" @selected(old('payment_policy', $ratePlan->payment_policy) === $p)>{{ __('admin.rate_plans.payment_policy_value.'.$p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.rate_plans.cancellation_policy">{{ __('admin.rate_plans.cancellation_policy') }}</label>
                    <select name="cancellation_policy_id" class="form-select js-tom-select">
                        <option value="">{{ __('admin.common.none') }}</option>
                        @foreach($options['cancellation_policies'] as $cp)
                            <option value="{{ $cp->id }}" @selected(old('cancellation_policy_id', $ratePlan->cancellation_policy_id) == $cp->id)>{{ $cp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_refundable" value="0">
                        <input class="form-check-input" type="checkbox" name="is_refundable" value="1"
                               id="is_refundable" @checked(old('is_refundable', $ratePlan->is_refundable))>
                        <label class="form-check-label" for="is_refundable" data-i18n="admin.rate_plans.refundable">{{ __('admin.rate_plans.refundable') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0" data-i18n="admin.common.status">{{ __('admin.common.status') }}</h5>
            </div>
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                    @foreach($options['statuses'] as $s)
                        <input type="radio" class="btn-check" name="status" value="{{ $s }}"
                               id="status_{{ $s }}" @checked(old('status', $ratePlan->status) === $s)>
                        <label class="btn btn-outline-{{ $s === 'active' ? 'success' : 'secondary' }}" for="status_{{ $s }}">
                            <span data-i18n="admin.rate_plans.status_value.{{ $s }}">{{ __('admin.rate_plans.status_value.'.$s) }}</span>
                        </label>
                    @endforeach
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> <span data-i18n="admin.common.save">{{ __('admin.common.save') }}</span>
                    </button>
                    <a href="{{ route('admin.rate_plans.index') }}" class="btn btn-outline-secondary" data-i18n="admin.common.cancel">{{ __('admin.common.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Filter room types by selected property
    const propEl = document.getElementById('rate_plan_property_id');
    const rtEl = document.getElementById('rate_plan_room_type_id');
    function filterRoomTypes() {
        const selectedProperty = propEl ? propEl.value : '';
        if (!rtEl) return;
        const ts = rtEl.tomselect;
        // If TomSelect attached, iterate options
        const options = rtEl.tomselect ? rtEl.tomselect.options : null;
        if (options) {
            const filtered = Object.values(options).filter((opt) => {
                if (!selectedProperty) return true;
                if (!opt.$option) return true;
                return String(opt.$option.getAttribute('data-property')) === String(selectedProperty);
            });
            // Re-build dropdown items
            rtEl.tomselect.clearOptions();
            rtEl.tomselect.addOptions(filtered);
            rtEl.tomselect.refreshOptions(false);
        } else {
            Array.from(rtEl.options).forEach((opt) => {
                const propId = opt.getAttribute('data-property');
                opt.hidden = selectedProperty && propId && propId !== selectedProperty;
            });
        }
    }
    if (propEl) {
        propEl.addEventListener('change', filterRoomTypes);
        // Defer until TomSelect inits
        setTimeout(filterRoomTypes, 200);
    }
});
</script>
@endpush
