@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.rooms'))
@section('pageTitle', __('admin.nav.rooms'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.rooms') }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('admin.rooms.new') }}
    </a>
@endsection

@section('content')
@php
    $statusTones = [
        'available' => 'success',
        'occupied' => 'primary',
        'maintenance' => 'warning',
        'inactive' => 'secondary',
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach($statuses as $st)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle text-{{ $statusTones[$st] ?? 'secondary' }} bg-{{ $statusTones[$st] ?? 'secondary' }}-subtle d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                        <i class="bi bi-{{ ['available' => 'door-open', 'occupied' => 'person-fill', 'maintenance' => 'tools', 'inactive' => 'slash-circle'][$st] ?? 'door' }} fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase" data-i18n="admin.rooms.status.{{ $st }}">{{ __('admin.rooms.status.'.$st) }}</div>
                        <div class="fs-5 fw-bold">{{ $statusCounts[$st] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.rooms.index') }}" id="rooms-filter" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1" data-i18n="admin.nav.properties">{{ __('admin.nav.properties') }}</label>
                <select name="property_id" class="form-select js-tom-select" id="rooms-property-id">
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected($selectedPropertyId == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" data-i18n="admin.nav.room_types">{{ __('admin.nav.room_types') }}</label>
                <select name="room_type_id" class="form-select js-tom-select" id="rooms-room-type-id">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}" @selected($selectedRoomTypeId == $rt->id)>{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1" data-i18n="admin.common.status">{{ __('admin.common.status') }}</label>
                <select name="status" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected($selectedStatus === $s)>
                            {{ __('admin.rooms.status.'.$s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary flex-fill">{{ __('admin.common.reset') }}</a>
                <button type="submit" class="btn btn-primary flex-fill">{{ __('admin.common.search') }}</button>
            </div>
        </form>
        <div class="d-flex justify-content-end mt-3">
            <div class="btn-group btn-group-sm" role="group" id="rooms-view-toggle">
                <button type="button" class="btn btn-primary" data-view="grid">
                    <i class="bi bi-grid-3x3-gap"></i> {{ __('admin.rooms.view_grid') }}
                </button>
                <button type="button" class="btn btn-outline-primary" data-view="table">
                    <i class="bi bi-table"></i> {{ __('admin.rooms.view_table') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="rooms-grid">
    @if($totalRooms === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-door-closed fs-1 d-block mb-2"></i>
                <p class="mb-2" data-i18n="admin.rooms.empty">{{ __('admin.rooms.empty') }}</p>
                <a href="{{ $createUrl }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('admin.rooms.new') }}</a>
            </div>
        </div>
    @else
        @foreach($floors as $floor => $rooms)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small text-muted" data-i18n="admin.rooms.floor">{{ __('admin.rooms.floor') }}:</span>
                        <strong class="ms-1">{{ $floor }}</strong>
                    </div>
                    <span class="text-muted small">{{ $rooms->count() }} {{ __('admin.properties.rooms') }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($rooms as $room)
                            <div class="room-tile room-status-{{ $room->status }} dropdown" data-room-id="{{ $room->id }}">
                                <button class="room-tile__btn dropdown-toggle" data-bs-toggle="dropdown">
                                    <div class="fw-bold">{{ $room->room_number }}</div>
                                    <div class="small">{{ $room->roomType?->name }}</div>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.rooms.show', $room->id) }}"><i class="bi bi-eye me-1"></i> {{ __('admin.common.show') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.rooms.edit', $room->id) }}"><i class="bi bi-pencil me-1"></i> {{ __('admin.common.edit') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach($statuses as $st)
                                        <li>
                                            <button type="button" class="dropdown-item js-status-toggle" data-status="{{ $st }}">
                                                <i class="bi bi-{{ ['available' => 'door-open', 'occupied' => 'person-fill', 'maintenance' => 'tools', 'inactive' => 'slash-circle'][$st] ?? 'door' }} me-1"></i>
                                                {{ __('admin.rooms.set_status') }} → {{ __('admin.rooms.status.'.$st) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div id="rooms-table" class="card shadow-sm border-0 d-none">
    <div class="card-body">
        <table class="table table-hover align-middle js-datatable" data-url="{{ $datatableUrl }}"
               data-columns="{{ json_encode($columns) }}" data-order='[[0,"desc"]]'>
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('rooms-view-toggle');
    const grid = document.getElementById('rooms-grid');
    const table = document.getElementById('rooms-table');
    toggle.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            toggle.querySelectorAll('button').forEach(b => { b.classList.remove('btn-primary'); b.classList.add('btn-outline-primary'); });
            btn.classList.remove('btn-outline-primary'); btn.classList.add('btn-primary');
            const v = btn.dataset.view;
            grid.classList.toggle('d-none', v !== 'grid');
            table.classList.toggle('d-none', v !== 'table');
        });
    });

    // Cascading room type when property changes
    const propSel = document.getElementById('rooms-property-id');
    const rtSel = document.getElementById('rooms-room-type-id');
    if (propSel && rtSel) {
        propSel.addEventListener('change', async () => {
            const propId = propSel.value;
            if (!propId) return;
            try {
                const res = await fetch('{{ route('admin.rooms.room_types') }}?property_id=' + propId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                const ts = rtSel.tomselect;
                if (ts) {
                    ts.clear();
                    ts.clearOptions();
                    ts.addOption({ id: '', text: '{{ __('admin.common.all') }}' });
                    json.data.forEach(o => ts.addOption(o));
                    ts.refreshOptions(false);
                } else {
                    rtSel.innerHTML = '<option value="">{{ __('admin.common.all') }}</option>' +
                        json.data.map(o => `<option value="${o.id}">${o.text}</option>`).join('');
                }
            } catch (e) { console.error(e); }
        });
    }

    // Quick status toggle from tile dropdown
    document.querySelectorAll('.js-status-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
            const tile = btn.closest('.room-tile');
            const roomId = tile.dataset.roomId;
            const status = btn.dataset.status;
            try {
                const res = await fetch('/admin/rooms/' + roomId + '/status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ status }),
                });
                if (!res.ok) throw new Error('Status update failed');
                tile.className = 'room-tile room-status-' + status + ' dropdown';
                tile.dataset.roomId = roomId;
                if (window.Swal) {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: '{{ __('admin.rooms.status_updated') }}',
                        showConfirmButton: false, timer: 1500,
                    });
                }
            } catch (e) {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Failed', text: e.message });
                }
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.room-tile { display: inline-block; }
.room-tile__btn { border: 1px solid rgba(0,0,0,.1); border-radius: 8px; padding: 10px 14px; min-width: 110px; text-align: center; cursor: pointer; }
.room-status-available .room-tile__btn { background:#d1f7d6; color:#0a4f1d; }
.room-status-occupied  .room-tile__btn { background:#cfe2ff; color:#0a3a8c; }
.room-status-maintenance .room-tile__btn { background:#fff3cd; color:#664d03; }
.room-status-inactive  .room-tile__btn { background:#e9ecef; color:#495057; }
</style>
@endpush
@endsection
