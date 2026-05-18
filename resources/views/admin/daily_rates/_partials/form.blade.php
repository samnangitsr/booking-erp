<form method="POST" action="{{ $formAction }}" class="row g-3">
    @csrf
    @if($formMethod === 'PUT') @method('PUT') @endif

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0" data-i18n="admin.daily_rates.row_basics">{{ __('admin.daily_rates.row_basics') }}</h5>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-8">
                    <label class="form-label" data-i18n="admin.nav.rate_plans">{{ __('admin.nav.rate_plans') }} <span class="text-danger">*</span></label>
                    <select name="rate_plan_id" class="form-select js-tom-select" required>
                        <option value="">{{ __('admin.common.select') }}</option>
                        @foreach($ratePlans as $rp)
                            <option value="{{ $rp->id }}" @selected(old('rate_plan_id', $dailyRate->rate_plan_id) == $rp->id)>{{ $rp->name }} <small class="text-muted">— {{ $rp->roomType?->name }}</small></option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="admin.daily_rates.date">{{ __('admin.daily_rates.date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="rate_date" value="{{ old('rate_date', optional($dailyRate->rate_date)->toDateString()) }}" class="form-control js-flatpickr-date" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.base_price">{{ __('admin.daily_rates.base_price') }} <span class="text-danger">*</span></label>
                    <input type="number" name="base_price" step="0.01" min="0" value="{{ old('base_price', $dailyRate->base_price) }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.adult_price">{{ __('admin.daily_rates.adult_price') }}</label>
                    <input type="number" name="adult_price" step="0.01" min="0" value="{{ old('adult_price', $dailyRate->adult_price) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.child_price">{{ __('admin.daily_rates.child_price') }}</label>
                    <input type="number" name="child_price" step="0.01" min="0" value="{{ old('child_price', $dailyRate->child_price) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.extra_bed_price">{{ __('admin.daily_rates.extra_bed_price') }}</label>
                    <input type="number" name="extra_bed_price" step="0.01" min="0" value="{{ old('extra_bed_price', $dailyRate->extra_bed_price) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.currency">{{ __('admin.daily_rates.currency') }}</label>
                    <input type="text" name="currency_code" maxlength="3" value="{{ old('currency_code', $dailyRate->currency_code ?: 'USD') }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.min_stay">{{ __('admin.daily_rates.min_stay') }}</label>
                    <input type="number" name="min_stay" min="1" value="{{ old('min_stay', $dailyRate->min_stay ?: 1) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label" data-i18n="admin.daily_rates.max_stay">{{ __('admin.daily_rates.max_stay') }}</label>
                    <input type="number" name="max_stay" min="1" value="{{ old('max_stay', $dailyRate->max_stay) }}" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0" data-i18n="admin.daily_rates.restrictions">{{ __('admin.daily_rates.restrictions') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="stop_sell" value="0">
                    <input class="form-check-input" type="checkbox" name="stop_sell" id="stop_sell" value="1" @checked(old('stop_sell', $dailyRate->stop_sell))>
                    <label class="form-check-label" for="stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="closed_to_arrival" value="0">
                    <input class="form-check-input" type="checkbox" name="closed_to_arrival" id="cta" value="1" @checked(old('closed_to_arrival', $dailyRate->closed_to_arrival))>
                    <label class="form-check-label" for="cta" data-i18n="admin.daily_rates.cta">{{ __('admin.daily_rates.cta') }}</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="closed_to_departure" value="0">
                    <input class="form-check-input" type="checkbox" name="closed_to_departure" id="ctd" value="1" @checked(old('closed_to_departure', $dailyRate->closed_to_departure))>
                    <label class="form-check-label" for="ctd" data-i18n="admin.daily_rates.ctd">{{ __('admin.daily_rates.ctd') }}</label>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> <span data-i18n="admin.common.save">{{ __('admin.common.save') }}</span>
                    </button>
                    <a href="{{ route('admin.daily_rates.index') }}" class="btn btn-outline-secondary" data-i18n="admin.common.cancel">{{ __('admin.common.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>
