@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.daily_rates'))
@section('pageTitle', __('admin.nav.daily_rates'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.daily_rates') }}</li>
@endsection

@section('toolbar')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
        <i class="bi bi-lightning-charge"></i> <span data-i18n="admin.daily_rates.bulk_edit">{{ __('admin.daily_rates.bulk_edit') }}</span>
    </button>
@endsection

@push('styles')
<style>
    .rate-grid {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
        width: 100%;
    }
    .rate-grid th, .rate-grid td {
        padding: 0.5rem;
        border-bottom: 1px solid var(--bs-border-color);
        border-right: 1px solid var(--bs-border-color);
        vertical-align: middle;
        background: #fff;
    }
    .rate-grid thead th {
        background: var(--bs-light);
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .rate-grid .row-head {
        position: sticky;
        left: 0;
        background: var(--bs-light);
        font-weight: 500;
        z-index: 1;
        min-width: 220px;
    }
    .rate-cell {
        text-align: center;
        cursor: pointer;
        transition: background 120ms ease;
        min-width: 80px;
    }
    .rate-cell:hover { background: var(--bs-primary-bg-subtle); }
    .rate-cell.is-stop-sell { background: var(--bs-danger-bg-subtle); color: var(--bs-danger); }
    .rate-cell.is-empty { color: var(--bs-secondary); }
    .rate-cell.is-today { box-shadow: inset 0 0 0 2px var(--bs-primary); }
    .rate-cell.is-weekend { background: var(--bs-tertiary-bg); }
    .rate-cell .price { font-weight: 600; }
    .rate-cell .restriction-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; margin: 0 1px; }
    .rate-cell .restriction-dot.cta { background: var(--bs-warning); }
    .rate-cell .restriction-dot.ctd { background: var(--bs-info); }
    .grid-scroller { overflow-x: auto; }
</style>
@endpush

@section('content')
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.daily_rates.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</label>
                <select name="property_id" class="form-select js-tom-select" onchange="this.form.submit()">
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected($propertyId == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.daily_rates.start_date">{{ __('admin.daily_rates.start_date') }}</label>
                <input type="text" name="start" value="{{ $start->toDateString() }}" class="form-control js-flatpickr-date">
            </div>
            <div class="col-md-5 d-flex gap-2">
                <a href="{{ route('admin.daily_rates.index', ['property_id' => $propertyId, 'start' => $prevStart->toDateString()]) }}"
                   class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-search"></i> <span data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</span>
                </button>
                <a href="{{ route('admin.daily_rates.index', ['property_id' => $propertyId, 'start' => $nextStart->toDateString()]) }}"
                   class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
            </div>
        </form>
        <div class="text-muted small mt-2">
            <span data-i18n="admin.daily_rates.range">{{ __('admin.daily_rates.range') }}</span>: <strong>{{ $start->format('M j') }}</strong> → <strong>{{ $end->format('M j, Y') }}</strong>
        </div>
    </div>
</div>

@if($ratePlans->isEmpty())
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-cash-stack fs-1 d-block mb-3"></i>
            <span data-i18n="admin.daily_rates.no_rate_plans">{{ __('admin.daily_rates.no_rate_plans') }}</span>
            <div class="mt-3">
                <a href="{{ route('admin.rate_plans.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> <span data-i18n="admin.rate_plans.new">{{ __('admin.rate_plans.new') }}</span>
                </a>
            </div>
        </div>
    </div>
@else
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="grid-scroller">
            <table class="rate-grid">
                <thead>
                    <tr>
                        <th class="row-head" data-i18n="admin.nav.rate_plans">{{ __('admin.nav.rate_plans') }}</th>
                        @foreach($dates as $d)
                            <th class="text-center {{ in_array($d->dayOfWeek, [0, 6]) ? 'text-primary' : '' }}">
                                <div class="small text-muted text-uppercase">{{ $d->format('D') }}</div>
                                <div class="fw-semibold">{{ $d->format('M j') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($ratePlans as $rp)
                        <tr>
                            <td class="row-head">
                                <a href="{{ route('admin.rate_plans.show', $rp->id) }}" class="text-decoration-none">
                                    <div class="fw-semibold">{{ $rp->name }}</div>
                                    <div class="small text-muted">{{ $rp->roomType?->name }}</div>
                                </a>
                            </td>
                            @foreach($dates as $d)
                                @php
                                    $key = $rp->id.'|'.$d->toDateString();
                                    $row = $rates->get($key)?->first();
                                    $isWeekend = in_array($d->dayOfWeek, [0, 6]);
                                    $isToday = $d->isSameDay($today);
                                    $classes = ['rate-cell'];
                                    if ($row?->stop_sell) $classes[] = 'is-stop-sell';
                                    if (! $row) $classes[] = 'is-empty';
                                    if ($isToday) $classes[] = 'is-today';
                                    if ($isWeekend) $classes[] = 'is-weekend';
                                @endphp
                                <td class="{{ implode(' ', $classes) }}"
                                    data-rate-plan="{{ $rp->id }}"
                                    data-rate-date="{{ $d->toDateString() }}"
                                    data-base-price="{{ $row?->base_price }}"
                                    data-stop-sell="{{ $row?->stop_sell ? '1' : '0' }}"
                                    data-min-stay="{{ $row?->min_stay ?? 1 }}"
                                    title="{{ $d->format('Y-m-d') }} — {{ $rp->name }}">
                                    @if($row?->stop_sell)
                                        <div class="price"><i class="bi bi-slash-circle"></i></div>
                                    @elseif($row?->base_price)
                                        <div class="price">${{ number_format((float) $row->base_price, 2) }}</div>
                                    @else
                                        <div class="price">—</div>
                                    @endif
                                    <div class="small">
                                        @if($row?->closed_to_arrival) <span class="restriction-dot cta" title="CTA"></span> @endif
                                        @if($row?->closed_to_departure) <span class="restriction-dot ctd" title="CTD"></span> @endif
                                        @if($row && $row->min_stay > 1) <span class="text-muted small">≥{{ $row->min_stay }}d</span> @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="d-flex flex-wrap gap-3 mt-3 small text-muted">
    <span><span class="restriction-dot cta" style="width:8px;height:8px;border-radius:50%;display:inline-block;background:var(--bs-warning)"></span> <span data-i18n="admin.daily_rates.legend_cta">{{ __('admin.daily_rates.legend_cta') }}</span></span>
    <span><span class="restriction-dot ctd" style="width:8px;height:8px;border-radius:50%;display:inline-block;background:var(--bs-info)"></span> <span data-i18n="admin.daily_rates.legend_ctd">{{ __('admin.daily_rates.legend_ctd') }}</span></span>
    <span><i class="bi bi-slash-circle text-danger"></i> <span data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</span></span>
</div>

@include('admin.daily_rates._partials.bulk_modal')

<!-- Cell editor popover -->
<div class="modal fade" id="cellEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cell-edit-form">
                <input type="hidden" name="rate_plan_id">
                <input type="hidden" name="rate_date">
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="admin.daily_rates.cell_title">{{ __('admin.daily_rates.cell_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.daily_rates.base_price">{{ __('admin.daily_rates.base_price') }}</label>
                        <input type="number" name="base_price" step="0.01" min="0" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.daily_rates.min_stay">{{ __('admin.daily_rates.min_stay') }}</label>
                        <input type="number" name="min_stay" min="1" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="stop_sell" id="cell_stop_sell" value="1">
                        <label class="form-check-label" for="cell_stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-i18n="admin.common.cancel">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> <span data-i18n="admin.common.save">{{ __('admin.common.save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cellModalEl = document.getElementById('cellEditModal');
    const cellModal = new bootstrap.Modal(cellModalEl);
    const cellForm = document.getElementById('cell-edit-form');
    const bulkForm = document.getElementById('bulk-edit-form');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.rate-cell').forEach((cell) => {
        cell.addEventListener('click', () => {
            cellForm.elements.rate_plan_id.value = cell.dataset.ratePlan;
            cellForm.elements.rate_date.value = cell.dataset.rateDate;
            cellForm.elements.base_price.value = cell.dataset.basePrice || '';
            cellForm.elements.min_stay.value = cell.dataset.minStay || 1;
            cellForm.elements.stop_sell.checked = cell.dataset.stopSell === '1';
            cellModal.show();
        });
    });

    cellForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            rate_plan_id: cellForm.elements.rate_plan_id.value,
            rate_date: cellForm.elements.rate_date.value,
            base_price: cellForm.elements.base_price.value || null,
            min_stay: cellForm.elements.min_stay.value || 1,
            stop_sell: cellForm.elements.stop_sell.checked ? 1 : 0,
        };
        const res = await fetch(@json(route('admin.daily_rates.cell')), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.ok) {
            cellModal.hide();
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(__('admin.daily_rates.saved')), showConfirmButton: false, timer: 1500 });
            setTimeout(() => window.location.reload(), 400);
        } else {
            Swal.fire({ icon: 'error', title: @json(__('admin.common.error')), text: data.message ?? '' });
        }
    });

    bulkForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(bulkForm);
        const payload = {};
        const flagKeys = ['stop_sell', 'closed_to_arrival', 'closed_to_departure'];
        for (const [k, v] of fd.entries()) {
            if (k === 'days_of_week[]') {
                payload.days_of_week = payload.days_of_week || [];
                payload.days_of_week.push(v);
            } else if (k.endsWith('_present') || k.endsWith('_apply')) {
                continue;
            } else if (flagKeys.includes(k)) {
                continue;
            } else if (v === '' && k !== '_token') {
                continue;
            } else {
                payload[k] = v;
            }
        }
        // Only send boolean toggles when the user explicitly opted in via the matching "apply" checkbox.
        flagKeys.forEach((key) => {
            if (bulkForm.elements[`${key}_apply`]?.checked) {
                payload[key] = bulkForm.elements[key]?.checked ? 1 : 0;
            }
        });
        const res = await fetch(@json(route('admin.daily_rates.bulk')), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.ok) {
            Swal.fire({ icon: 'success', title: data.message, timer: 1800, showConfirmButton: false });
            setTimeout(() => window.location.reload(), 500);
        } else {
            Swal.fire({ icon: 'error', title: @json(__('admin.common.error')), text: data.message ?? '' });
        }
    });
});
</script>
@endpush
@endsection
