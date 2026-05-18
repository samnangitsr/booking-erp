@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.availability'))
@section('pageTitle', __('admin.nav.availability'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.availability') }}</li>
@endsection

@section('toolbar')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAvailModal">
        <i class="bi bi-lightning-charge"></i> <span data-i18n="admin.availability.bulk_edit">{{ __('admin.availability.bulk_edit') }}</span>
    </button>
@endsection

@push('styles')
<style>
    .avail-grid {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
        width: 100%;
    }
    .avail-grid th, .avail-grid td {
        padding: 0.4rem;
        border-bottom: 1px solid var(--bs-border-color);
        border-right: 1px solid var(--bs-border-color);
        vertical-align: middle;
        background: #fff;
    }
    .avail-grid thead th {
        background: var(--bs-light);
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .avail-grid .row-head {
        position: sticky;
        left: 0;
        background: var(--bs-light);
        font-weight: 500;
        z-index: 1;
        min-width: 220px;
    }
    .avail-cell {
        text-align: center;
        cursor: pointer;
        transition: background 120ms ease;
        min-width: 86px;
    }
    .avail-cell:hover { filter: brightness(0.95); }
    .avail-cell.heat-0 { background: #f8f9fa; color: var(--bs-secondary); }
    .avail-cell.heat-1 { background: #d1f4dc; }
    .avail-cell.heat-2 { background: #b2eecb; }
    .avail-cell.heat-3 { background: #ffe6a8; }
    .avail-cell.heat-4 { background: #ffcdd2; color: #842029; }
    .avail-cell.is-stop-sell { background: #f5c2c7 !important; color: #842029; }
    .avail-cell.is-today { box-shadow: inset 0 0 0 2px var(--bs-primary); }
    .avail-cell .num { font-weight: 600; font-size: 1rem; line-height: 1.1; }
    .avail-cell .breakdown { font-size: 0.7rem; opacity: 0.7; }
    .grid-scroller { overflow-x: auto; }
</style>
@endpush

@section('content')
<div class="row g-3 mb-3">
    @php
        $cards = [
            ['key' => 'available', 'value' => $summary['available'], 'icon' => 'door-open', 'tone' => 'success'],
            ['key' => 'booked', 'value' => $summary['booked'], 'icon' => 'calendar-check', 'tone' => 'primary'],
            ['key' => 'blocked', 'value' => $summary['blocked'], 'icon' => 'shield-lock', 'tone' => 'warning'],
            ['key' => 'stop_sell', 'value' => $summary['stop_sell'], 'icon' => 'slash-circle', 'tone' => 'danger'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle"
                             style="width:42px;height:42px;">
                            <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase" data-i18n="admin.availability.stats.{{ $card['key'] }}">
                                {{ __('admin.availability.stats.'.$card['key']) }}
                            </div>
                            <div class="fw-bold fs-5 mb-0">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.availability_calendars.index') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('admin.availability_calendars.index', ['property_id' => $propertyId, 'start' => $prevStart->toDateString()]) }}"
                   class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-search"></i> <span data-i18n="admin.common.apply">{{ __('admin.common.apply') }}</span>
                </button>
                <a href="{{ route('admin.availability_calendars.index', ['property_id' => $propertyId, 'start' => $nextStart->toDateString()]) }}"
                   class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
            </div>
        </form>
        <div class="text-muted small mt-2">
            <span data-i18n="admin.daily_rates.range">{{ __('admin.daily_rates.range') }}</span>: <strong>{{ $start->format('M j') }}</strong> → <strong>{{ $end->format('M j, Y') }}</strong>
        </div>
    </div>
</div>

@if($roomTypes->isEmpty())
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
            <span data-i18n="admin.availability.no_room_types">{{ __('admin.availability.no_room_types') }}</span>
        </div>
    </div>
@else
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="grid-scroller">
            <table class="avail-grid">
                <thead>
                    <tr>
                        <th class="row-head" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}</th>
                        @foreach($dates as $d)
                            <th class="text-center {{ in_array($d->dayOfWeek, [0, 6]) ? 'text-primary' : '' }}">
                                <div class="small text-muted text-uppercase">{{ $d->format('D') }}</div>
                                <div class="fw-semibold">{{ $d->format('M j') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomTypes as $rt)
                        <tr>
                            <td class="row-head">
                                <a href="{{ route('admin.room_types.show', $rt->id) }}" class="text-decoration-none">
                                    <div class="fw-semibold">{{ $rt->name }}</div>
                                    <div class="small text-muted">{{ $rt->total_rooms ?? '—' }} {{ __('admin.availability.total_short') }}</div>
                                </a>
                            </td>
                            @foreach($dates as $d)
                                @php
                                    $key = $rt->id.'|'.$d->toDateString();
                                    $row = $rows->get($key)?->first();
                                    $total = $row?->total_rooms ?? (int) ($rt->total_rooms ?? 0);
                                    $available = $row?->available_rooms ?? $total;
                                    $ratio = $total > 0 ? $available / $total : 0;
                                    $heat = match (true) {
                                        $total === 0 => 0,
                                        $ratio >= 0.75 => 1,
                                        $ratio >= 0.5 => 2,
                                        $ratio >= 0.25 => 3,
                                        default => 4,
                                    };
                                    $classes = ['avail-cell', "heat-{$heat}"];
                                    if ($row?->stop_sell) $classes[] = 'is-stop-sell';
                                    if ($d->isSameDay($today)) $classes[] = 'is-today';
                                @endphp
                                <td class="{{ implode(' ', $classes) }}"
                                    data-room-type="{{ $rt->id }}"
                                    data-available-date="{{ $d->toDateString() }}"
                                    data-total="{{ $total }}"
                                    data-blocked="{{ $row?->blocked_rooms ?? 0 }}"
                                    data-stop-sell="{{ $row?->stop_sell ? '1' : '0' }}"
                                    title="{{ $d->format('Y-m-d') }} — {{ $rt->name }}">
                                    <div class="num">
                                        @if($row?->stop_sell)
                                            <i class="bi bi-slash-circle"></i>
                                        @else
                                            {{ $available }}
                                        @endif
                                    </div>
                                    <div class="breakdown">{{ $row?->booked_rooms ?? 0 }}b · {{ $row?->blocked_rooms ?? 0 }}x</div>
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

<div class="d-flex flex-wrap gap-2 mt-3 small text-muted align-items-center">
    <span data-i18n="admin.availability.legend">{{ __('admin.availability.legend') }}:</span>
    <span class="badge heat-1" style="background:#d1f4dc;color:#0a3622">≥ 75%</span>
    <span class="badge heat-2" style="background:#b2eecb;color:#0a3622">≥ 50%</span>
    <span class="badge heat-3" style="background:#ffe6a8;color:#5c3c00">≥ 25%</span>
    <span class="badge heat-4" style="background:#ffcdd2;color:#842029">&lt; 25%</span>
    <span class="badge" style="background:#f5c2c7;color:#842029"><i class="bi bi-slash-circle"></i> <span data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</span></span>
</div>

@include('admin.availability_calendars._partials.bulk_modal')

<!-- Cell editor -->
<div class="modal fade" id="availCellModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="avail-cell-form">
                <input type="hidden" name="room_type_id">
                <input type="hidden" name="available_date">
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="admin.availability.cell_title">{{ __('admin.availability.cell_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.availability.total_rooms">{{ __('admin.availability.total_rooms') }}</label>
                        <input type="number" name="total_rooms" min="0" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" data-i18n="admin.availability.blocked_rooms">{{ __('admin.availability.blocked_rooms') }}</label>
                        <input type="number" name="blocked_rooms" min="0" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="stop_sell" id="avail_cell_stop_sell" value="1">
                        <label class="form-check-label" for="avail_cell_stop_sell" data-i18n="admin.daily_rates.stop_sell">{{ __('admin.daily_rates.stop_sell') }}</label>
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
    const cellModalEl = document.getElementById('availCellModal');
    const cellModal = new bootstrap.Modal(cellModalEl);
    const cellForm = document.getElementById('avail-cell-form');
    const bulkForm = document.getElementById('bulk-avail-form');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.avail-cell').forEach((cell) => {
        cell.addEventListener('click', () => {
            cellForm.elements.room_type_id.value = cell.dataset.roomType;
            cellForm.elements.available_date.value = cell.dataset.availableDate;
            cellForm.elements.total_rooms.value = cell.dataset.total;
            cellForm.elements.blocked_rooms.value = cell.dataset.blocked;
            cellForm.elements.stop_sell.checked = cell.dataset.stopSell === '1';
            cellModal.show();
        });
    });

    cellForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            room_type_id: cellForm.elements.room_type_id.value,
            available_date: cellForm.elements.available_date.value,
            total_rooms: cellForm.elements.total_rooms.value || 0,
            blocked_rooms: cellForm.elements.blocked_rooms.value || 0,
            stop_sell: cellForm.elements.stop_sell.checked ? 1 : 0,
        };
        const res = await fetch(@json(route('admin.availability_calendars.cell')), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.ok) {
            cellModal.hide();
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(__('admin.availability.saved')), showConfirmButton: false, timer: 1500 });
            setTimeout(() => window.location.reload(), 400);
        } else {
            Swal.fire({ icon: 'error', title: @json(__('admin.common.error')), text: data.message ?? '' });
        }
    });

    bulkForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(bulkForm);
        const payload = {};
        for (const [k, v] of fd.entries()) {
            if (k === 'days_of_week[]') {
                payload.days_of_week = payload.days_of_week || [];
                payload.days_of_week.push(v);
            } else if (k === 'stop_sell' || k.endsWith('_apply')) {
                continue;
            } else if (v === '' && k !== '_token') {
                continue;
            } else {
                payload[k] = v;
            }
        }
        // Only send stop_sell when the user explicitly opts in via the matching "apply" checkbox.
        if (bulkForm.elements.stop_sell_apply?.checked) {
            payload.stop_sell = bulkForm.elements.stop_sell.checked ? 1 : 0;
        }
        const res = await fetch(@json(route('admin.availability_calendars.bulk')), {
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
