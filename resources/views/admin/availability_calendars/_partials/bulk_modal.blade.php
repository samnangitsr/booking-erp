<div class="modal fade" id="bulkAvailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bulk-avail-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="admin.availability.bulk_title">{{ __('admin.availability.bulk_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}</label>
                            <select name="room_type_id" class="form-select" required>
                                <option value="">{{ __('admin.common.select') }}</option>
                                @foreach($roomTypes as $rt)
                                    <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.daily_rates.start_date">{{ __('admin.daily_rates.start_date') }}</label>
                            <input type="text" name="start_date" class="form-control js-flatpickr-date" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" data-i18n="admin.daily_rates.end_date">{{ __('admin.daily_rates.end_date') }}</label>
                            <input type="text" name="end_date" class="form-control js-flatpickr-date" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" data-i18n="admin.daily_rates.days_of_week">{{ __('admin.daily_rates.days_of_week') }}</label>
                            <div class="btn-group" role="group">
                                @foreach([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 0 => 'Sun'] as $idx => $label)
                                    <input type="checkbox" class="btn-check" id="avail-dow-{{ $idx }}" name="days_of_week[]" value="{{ $idx }}">
                                    <label class="btn btn-outline-secondary" for="avail-dow-{{ $idx }}">{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.availability.total_rooms">{{ __('admin.availability.total_rooms') }}</label>
                            <input type="number" name="total_rooms" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.availability.blocked_rooms">{{ __('admin.availability.blocked_rooms') }}</label>
                            <input type="number" name="blocked_rooms" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2 pt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stop_sell_apply" id="avail_apply_bulk_stop_sell" value="1">
                                    <label class="form-check-label small text-muted" for="avail_apply_bulk_stop_sell" data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="stop_sell" id="avail_bulk_stop_sell" value="1">
                                    <label class="form-check-label" for="avail_bulk_stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-i18n="admin.common.cancel">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-lightning-charge"></i> <span data-i18n="admin.daily_rates.apply">{{ __('admin.daily_rates.apply') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
