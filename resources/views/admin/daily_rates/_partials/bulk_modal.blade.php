<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bulk-edit-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="admin.daily_rates.bulk_title">{{ __('admin.daily_rates.bulk_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" data-i18n="admin.nav.rate_plans">{{ __('admin.nav.rate_plans') }}</label>
                            <select name="rate_plan_id" class="form-select" required>
                                <option value="">{{ __('admin.common.select') }}</option>
                                @foreach($ratePlans as $rp)
                                    <option value="{{ $rp->id }}">{{ $rp->name }} <small class="text-muted">— {{ $rp->roomType?->name }}</small></option>
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
                                    <input type="checkbox" class="btn-check" id="dow-{{ $idx }}" name="days_of_week[]" value="{{ $idx }}">
                                    <label class="btn btn-outline-secondary" for="dow-{{ $idx }}">{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.base_price">{{ __('admin.daily_rates.base_price') }}</label>
                            <input type="number" name="base_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.adult_price">{{ __('admin.daily_rates.adult_price') }}</label>
                            <input type="number" name="adult_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.child_price">{{ __('admin.daily_rates.child_price') }}</label>
                            <input type="number" name="child_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.extra_bed_price">{{ __('admin.daily_rates.extra_bed_price') }}</label>
                            <input type="number" name="extra_bed_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.min_stay">{{ __('admin.daily_rates.min_stay') }}</label>
                            <input type="number" name="min_stay" min="1" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" data-i18n="admin.daily_rates.max_stay">{{ __('admin.daily_rates.max_stay') }}</label>
                            <input type="number" name="max_stay" min="1" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-2" data-i18n="admin.daily_rates.restrictions_hint">{{ __('admin.daily_rates.restrictions_hint') }}</div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="stop_sell_apply" id="apply_bulk_stop_sell" value="1">
                                            <label class="form-check-label small text-muted" for="apply_bulk_stop_sell" data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="stop_sell" id="bulk_stop_sell" value="1">
                                            <label class="form-check-label" for="bulk_stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="closed_to_arrival_apply" id="apply_bulk_cta" value="1">
                                            <label class="form-check-label small text-muted" for="apply_bulk_cta" data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="closed_to_arrival" id="bulk_cta" value="1">
                                            <label class="form-check-label" for="bulk_cta" data-i18n="admin.daily_rates.cta">{{ __('admin.daily_rates.cta') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="closed_to_departure_apply" id="apply_bulk_ctd" value="1">
                                            <label class="form-check-label small text-muted" for="apply_bulk_ctd" data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="closed_to_departure" id="bulk_ctd" value="1">
                                            <label class="form-check-label" for="bulk_ctd" data-i18n="admin.daily_rates.ctd">{{ __('admin.daily_rates.ctd') }}</label>
                                        </div>
                                    </div>
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
