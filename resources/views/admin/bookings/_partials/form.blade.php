@php
    /** @var \App\Models\Booking $booking */
    /** @var \Illuminate\Support\Collection $items */
    /** @var array $options */
    $items = $items ?? collect();
    $customers = $options['customers'] ?? collect();
    $properties = $options['properties'] ?? collect();
    $branches = $options['branches'] ?? collect();
    $roomTypes = $options['roomTypes'] ?? collect();
    $sources = $options['sources'] ?? \App\Models\Booking::SOURCES;
    $statuses = $options['statuses'] ?? \App\Models\Booking::STATUSES;
    $paymentStatuses = $options['paymentStatuses'] ?? \App\Models\Booking::PAYMENT_STATUSES;
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="bi bi-person-vcard text-primary me-1"></i>
                    {{ __('admin.bookings.section.guest_stay') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">{{ __('admin.bookings.guest') }} <span class="text-danger">*</span></label>
                        <select id="customer_id" name="customer_id" class="form-select js-tom-select" required>
                            <option value="">{{ __('admin.common.select_one') }}</option>
                            @foreach($customers as $c)
                                @php $val = old('customer_id', $booking->customer_id); @endphp
                                <option value="{{ $c->id }}" @selected((string) $val === (string) $c->id)>
                                    {{ trim($c->first_name.' '.$c->last_name) }} @if($c->phone) — {{ $c->phone }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="property_id" class="form-label">{{ __('admin.nav.properties') }} <span class="text-danger">*</span></label>
                        <select id="property_id" name="property_id" class="form-select js-tom-select" required>
                            <option value="">{{ __('admin.common.select_one') }}</option>
                            @foreach($properties as $p)
                                <option value="{{ $p->id }}" @selected((string) old('property_id', $booking->property_id) === (string) $p->id)>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="check_in_date" class="form-label">{{ __('admin.bookings.check_in') }} <span class="text-danger">*</span></label>
                        <input type="text" id="check_in_date" name="check_in_date"
                               value="{{ old('check_in_date', optional($booking->check_in_date)->format('Y-m-d')) }}"
                               class="form-control js-flatpickr-date" required>
                    </div>
                    <div class="col-md-4">
                        <label for="check_out_date" class="form-label">{{ __('admin.bookings.check_out') }} <span class="text-danger">*</span></label>
                        <input type="text" id="check_out_date" name="check_out_date"
                               value="{{ old('check_out_date', optional($booking->check_out_date)->format('Y-m-d')) }}"
                               class="form-control js-flatpickr-date" required>
                    </div>
                    <div class="col-md-2">
                        <label for="total_adults" class="form-label">{{ __('admin.bookings.adults') }}</label>
                        <input type="number" id="total_adults" name="total_adults" min="1"
                               value="{{ old('total_adults', $booking->total_adults ?? 1) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="total_children" class="form-label">{{ __('admin.bookings.children') }}</label>
                        <input type="number" id="total_children" name="total_children" min="0"
                               value="{{ old('total_children', $booking->total_children ?? 0) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">{{ __('admin.nav.branches') }}</label>
                        <select id="branch_id" name="branch_id" class="form-select js-tom-select">
                            <option value="">—</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" @selected((string) old('branch_id', $booking->branch_id) === (string) $b->id)>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="booking_source" class="form-label">{{ __('admin.bookings.source') }}</label>
                        <select id="booking_source" name="booking_source" class="form-select js-tom-select">
                            @foreach($sources as $s)
                                <option value="{{ $s }}" @selected(old('booking_source', $booking->booking_source) === $s)>
                                    {{ __('admin.bookings.source_label.'.$s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="currency_code" class="form-label">{{ __('admin.bookings.currency') }}</label>
                        <input type="text" id="currency_code" name="currency_code"
                               value="{{ old('currency_code', $booking->currency_code ?? 'USD') }}"
                               class="form-control" maxlength="10">
                    </div>
                    <div class="col-12">
                        <label for="special_request" class="form-label">{{ __('admin.bookings.special_request') }}</label>
                        <textarea id="special_request" name="special_request" rows="2"
                                  class="form-control">{{ old('special_request', $booking->special_request) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3" id="items-card">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-door-closed text-primary me-1"></i>
                    {{ __('admin.bookings.section.items') }}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="btn-add-item">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('admin.bookings.add_item') }}
                </button>
            </div>
            <div class="card-body">
                <div id="items-list" class="d-flex flex-column gap-3">
                    @php $initialItems = old('items'); @endphp
                    @if(empty($initialItems) && $items->isEmpty())
                        {{-- start with one blank --}}
                    @endif
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle me-1"></i>{{ __('admin.bookings.items_help') }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3 sticky-summary">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="bi bi-receipt text-primary me-1"></i>
                    {{ __('admin.bookings.section.summary') }}
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted">{{ __('admin.bookings.nights') }}</dt>
                    <dd class="col-6 text-end fw-medium" id="summary-nights">1</dd>

                    <dt class="col-6 text-muted">{{ __('admin.bookings.rooms') }}</dt>
                    <dd class="col-6 text-end fw-medium" id="summary-rooms">0</dd>

                    <dt class="col-6 text-muted">{{ __('admin.bookings.subtotal') }}</dt>
                    <dd class="col-6 text-end fw-medium" id="summary-subtotal">0.00</dd>

                    <dt class="col-6">
                        <label for="discount_amount" class="form-label small text-muted mb-0">{{ __('admin.bookings.discount') }}</label>
                    </dt>
                    <dd class="col-6 text-end">
                        <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount"
                               value="{{ old('discount_amount', $booking->discount_amount ?? 0) }}"
                               class="form-control form-control-sm text-end js-recompute" data-role="discount">
                    </dd>

                    <dt class="col-6">
                        <label for="tax_amount" class="form-label small text-muted mb-0">{{ __('admin.bookings.tax') }}</label>
                    </dt>
                    <dd class="col-6 text-end">
                        <input type="number" step="0.01" min="0" id="tax_amount" name="tax_amount"
                               value="{{ old('tax_amount', $booking->tax_amount ?? 0) }}"
                               class="form-control form-control-sm text-end js-recompute" data-role="tax">
                    </dd>

                    <dt class="col-6">
                        <label for="fee_amount" class="form-label small text-muted mb-0">{{ __('admin.bookings.fees') }}</label>
                    </dt>
                    <dd class="col-6 text-end">
                        <input type="number" step="0.01" min="0" id="fee_amount" name="fee_amount"
                               value="{{ old('fee_amount', $booking->fee_amount ?? 0) }}"
                               class="form-control form-control-sm text-end js-recompute" data-role="fee">
                    </dd>

                    <dt class="col-6 fw-bold text-primary">{{ __('admin.bookings.grand_total') }}</dt>
                    <dd class="col-6 text-end fw-bold text-primary fs-5" id="summary-grand">0.00</dd>

                    <dt class="col-6">
                        <label for="paid_amount" class="form-label small text-muted mb-0">{{ __('admin.bookings.paid') }}</label>
                    </dt>
                    <dd class="col-6 text-end">
                        <input type="number" step="0.01" min="0" id="paid_amount" name="paid_amount"
                               value="{{ old('paid_amount', $booking->paid_amount ?? 0) }}"
                               class="form-control form-control-sm text-end js-recompute" data-role="paid">
                    </dd>

                    <dt class="col-6 text-muted">{{ __('admin.bookings.due') }}</dt>
                    <dd class="col-6 text-end fw-medium text-danger" id="summary-due">0.00</dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="bi bi-flag text-primary me-1"></i>
                    {{ __('admin.common.status') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="booking_status" class="form-label">{{ __('admin.bookings.status_label') }}</label>
                    <select id="booking_status" name="booking_status" class="form-select js-tom-select">
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(old('booking_status', $booking->booking_status ?? 'pending') === $s)>
                                {{ __('admin.bookings.status.'.$s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="payment_status" class="form-label">{{ __('admin.bookings.payment') }}</label>
                    <select id="payment_status" name="payment_status" class="form-select js-tom-select">
                        @foreach($paymentStatuses as $s)
                            <option value="{{ $s }}" @selected(old('payment_status', $booking->payment_status ?? 'unpaid') === $s)>
                                {{ __('admin.bookings.status.'.$s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="item-template">
    <div class="item-row border rounded p-3 bg-light-subtle">
        <div class="d-flex align-items-start mb-2">
            <div class="flex-grow-1">
                <h6 class="mb-0 small text-muted text-uppercase">
                    <span class="badge bg-secondary me-1 item-index-badge">#</span>
                    {{ __('admin.bookings.item_row') }}
                </h6>
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger js-remove-item p-0">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <input type="hidden" data-name="id" value="">
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label small mb-1">{{ __('admin.nav.room_types') }}</label>
                <select class="form-select form-select-sm js-item-room-type" data-name="room_type_id" required></select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">{{ __('admin.nav.rate_plans') }}</label>
                <select class="form-select form-select-sm js-item-rate-plan" data-name="rate_plan_id">
                    <option value="">—</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.bookings.rooms') }}</label>
                <input type="number" class="form-control form-control-sm js-recompute"
                       data-name="rooms_count" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">{{ __('admin.bookings.adults') }}</label>
                <input type="number" class="form-control form-control-sm" data-name="adults" min="1" value="1">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">{{ __('admin.bookings.children') }}</label>
                <input type="number" class="form-control form-control-sm" data-name="children" min="0" value="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">{{ __('admin.bookings.unit_price') }}</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm js-recompute"
                       data-name="unit_price" value="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1 text-muted">{{ __('admin.bookings.line_total') }}</label>
                <div class="form-control form-control-sm bg-white fw-semibold js-item-line-total">0.00</div>
            </div>
        </div>
    </div>
</template>

@php
    $existing = old('items', $items->map(function ($it) {
        return [
            'id' => $it->id,
            'room_type_id' => $it->room_type_id,
            'rate_plan_id' => $it->rate_plan_id,
            'rooms_count' => $it->rooms_count,
            'adults' => $it->adults,
            'children' => $it->children,
            'unit_price' => $it->unit_price,
        ];
    })->all());
    if (empty($existing)) {
        $existing = [['id' => '', 'room_type_id' => '', 'rate_plan_id' => '', 'rooms_count' => 1, 'adults' => 1, 'children' => 0, 'unit_price' => 0]];
    }
@endphp
@push('scripts')
<script>
window.__bookingForm = {
    propertyId: @json($booking->property_id ?? null),
    initialItems: @json($existing),
    roomTypesUrl: @json($options['roomTypesUrl']),
    ratePlansUrl: @json($options['ratePlansUrl']),
    initialRoomTypes: @json($roomTypes->map(fn($rt) => ['id' => $rt->id, 'text' => $rt->name, 'base_price' => (float) $rt->base_price])),
};
</script>
@vite('resources/js/admin/bookings-form.js')
@endpush
