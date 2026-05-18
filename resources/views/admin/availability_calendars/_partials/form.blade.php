<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">
                    <strong data-i18n="admin.common.basics">{{ __('admin.common.basics') }}</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}<span class="text-danger">*</span></label>
                            <select name="property_id" id="property_id" class="form-select js-tom-select" required>
                                <option value="">{{ __('admin.common.select') }}</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}" @selected(old('property_id', $availability->property_id) == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}<span class="text-danger">*</span></label>
                            <select name="room_type_id" id="room_type_id" class="form-select js-tom-select" required>
                                <option value="">{{ __('admin.common.select') }}</option>
                                @foreach($roomTypes as $rt)
                                    <option value="{{ $rt->id }}"
                                            data-property="{{ $rt->property_id }}"
                                            @selected(old('room_type_id', $availability->room_type_id) == $rt->id)>{{ $rt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.availability.available_date">{{ __('admin.availability.available_date') }}<span class="text-danger">*</span></label>
                            <input type="text" name="available_date"
                                   value="{{ old('available_date', optional($availability->available_date)->toDateString()) }}"
                                   class="form-control js-flatpickr-date" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="stop_sell" id="stop_sell" value="1"
                                       @checked(old('stop_sell', $availability->stop_sell))>
                                <label class="form-check-label" for="stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">
                    <strong data-i18n="admin.availability.inventory">{{ __('admin.availability.inventory') }}</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.availability.total_rooms">{{ __('admin.availability.total_rooms') }}<span class="text-danger">*</span></label>
                        <input type="number" min="0" name="total_rooms" value="{{ old('total_rooms', $availability->total_rooms ?? 0) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.availability.booked_rooms">{{ __('admin.availability.booked_rooms') }}</label>
                        <input type="number" min="0" name="booked_rooms" value="{{ old('booked_rooms', $availability->booked_rooms ?? 0) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.availability.blocked_rooms">{{ __('admin.availability.blocked_rooms') }}</label>
                        <input type="number" min="0" name="blocked_rooms" value="{{ old('blocked_rooms', $availability->blocked_rooms ?? 0) }}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.availability_calendars.index') }}" class="btn btn-light" data-i18n="admin.common.cancel">{{ __('admin.common.cancel') }}</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> <span data-i18n="admin.common.save">{{ __('admin.common.save') }}</span>
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const propEl = document.getElementById('property_id');
    const rtEl = document.getElementById('room_type_id');
    if (!propEl || !rtEl) return;
    function filterRt() {
        const pid = String(propEl.value || '');
        Array.from(rtEl.options).forEach((opt) => {
            if (!opt.value) return;
            opt.hidden = pid && opt.dataset.property !== pid;
        });
        if (rtEl.selectedOptions[0] && rtEl.selectedOptions[0].hidden) {
            rtEl.value = '';
        }
        if (rtEl.tomselect) {
            rtEl.tomselect.sync();
        }
    }
    propEl.addEventListener('change', filterRt);
    filterRt();
});
</script>
@endpush
