@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.bookings'))
@section('pageTitle', __('admin.nav.bookings'))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.bookings') }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('admin.bookings.new') }}
    </a>
@endsection

@section('content')
@php
    $statsCards = [
        ['key' => 'pending', 'value' => $stats['pending'], 'icon' => 'hourglass-split', 'tone' => 'warning'],
        ['key' => 'confirmed', 'value' => $stats['confirmed'], 'icon' => 'check2-circle', 'tone' => 'info'],
        ['key' => 'checked_in', 'value' => $stats['checked_in'], 'icon' => 'box-arrow-in-right', 'tone' => 'primary'],
        ['key' => 'arrivals_today', 'value' => $stats['arrivals_today'], 'icon' => 'calendar-event', 'tone' => 'success'],
        ['key' => 'departures_today', 'value' => $stats['departures_today'], 'icon' => 'calendar-x', 'tone' => 'secondary'],
        ['key' => 'revenue_paid', 'value' => number_format($stats['revenue_paid'], 2), 'icon' => 'cash-stack', 'tone' => 'success', 'is_money' => true],
        ['key' => 'revenue_due', 'value' => number_format($stats['revenue_due'], 2), 'icon' => 'wallet2', 'tone' => 'danger', 'is_money' => true],
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach($statsCards as $card)
        <div class="col-6 col-lg-3 col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center
                            text-{{ $card['tone'] }} bg-{{ $card['tone'] }}-subtle"
                             style="width:42px;height:42px;">
                            <i class="bi bi-{{ $card['icon'] }} fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase"
                                 data-i18n="admin.bookings.stats.{{ $card['key'] }}">
                                {{ __('admin.bookings.stats.'.$card['key']) }}
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
        <form id="bookings-filter" method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3 align-items-end">
            <div class="col-12">
                <div class="btn-toolbar flex-wrap gap-1" role="toolbar" aria-label="Status filter">
                    @php
                        $statusFilters = [
                            ''            => ['label' => __('admin.common.all'), 'tone' => 'dark'],
                            'pending'     => ['label' => __('admin.bookings.status.pending'), 'tone' => 'warning'],
                            'confirmed'   => ['label' => __('admin.bookings.status.confirmed'), 'tone' => 'info'],
                            'checked_in'  => ['label' => __('admin.bookings.status.checked_in'), 'tone' => 'primary'],
                            'checked_out' => ['label' => __('admin.bookings.status.checked_out'), 'tone' => 'success'],
                            'cancelled'   => ['label' => __('admin.bookings.status.cancelled'), 'tone' => 'danger'],
                            'no_show'     => ['label' => __('admin.bookings.status.no_show'), 'tone' => 'secondary'],
                        ];
                    @endphp
                    @foreach($statusFilters as $value => $f)
                        @php $active = (string) $filters['status'] === (string) $value; @endphp
                        <button type="button"
                                data-filter-status="{{ $value }}"
                                class="btn btn-sm rounded-pill px-3 js-status-pill
                                       {{ $active ? 'btn-'.$f['tone'] : 'btn-outline-'.$f['tone'] }}">
                            {{ $f['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="status" id="bookings-filter-status" value="{{ $filters['status'] }}">
            <div class="col-sm-6 col-lg-3">
                <label for="filter-payment" class="form-label small text-muted text-uppercase mb-1">
                    {{ __('admin.bookings.payment_status') }}
                </label>
                <select id="filter-payment" name="payment_status" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach(['unpaid', 'partial', 'paid', 'refunded'] as $opt)
                        <option value="{{ $opt }}" @selected($filters['payment_status'] === $opt)>
                            {{ __('admin.bookings.status.'.$opt) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="filter-date-from" class="form-label small text-muted text-uppercase mb-1">
                    {{ __('admin.bookings.date_from') }}
                </label>
                <input type="text" id="filter-date-from" name="date_from"
                       value="{{ $filters['date_from'] }}"
                       class="form-control js-flatpickr-date">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="filter-date-to" class="form-label small text-muted text-uppercase mb-1">
                    {{ __('admin.bookings.date_to') }}
                </label>
                <input type="text" id="filter-date-to" name="date_to"
                       value="{{ $filters['date_to'] }}"
                       class="form-control js-flatpickr-date">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="filter-property" class="form-label small text-muted text-uppercase mb-1">
                    {{ __('admin.nav.properties') }}
                </label>
                <select id="filter-property" name="property_id" class="form-select js-tom-select">
                    <option value="">{{ __('admin.common.all') }}</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected((string) $filters['property_id'] === (string) $p->id)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-link text-muted">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('admin.common.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>{{ __('admin.common.search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            @php
                $columns = [
                    ['data' => 'id',             'name' => 'id',             'title' => '#', 'width' => '60px'],
                    ['data' => 'booking_no',     'name' => 'booking_no',     'title' => __('admin.bookings.booking_no')],
                    ['data' => 'customer_name',  'name' => 'customer_name',  'title' => __('admin.bookings.guest'),     'orderable' => false],
                    ['data' => 'property_name',  'name' => 'property_name',  'title' => __('admin.nav.properties'),     'orderable' => false],
                    ['data' => 'check_in_date',  'name' => 'check_in_date',  'title' => __('admin.bookings.check_in')],
                    ['data' => 'check_out_date', 'name' => 'check_out_date', 'title' => __('admin.bookings.check_out')],
                    ['data' => 'grand_total',    'name' => 'grand_total',    'title' => __('admin.bookings.total'),     'className' => 'text-end'],
                    ['data' => 'booking_status', 'name' => 'booking_status', 'title' => __('admin.bookings.status_label')],
                    ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('admin.bookings.payment')],
                    ['data' => 'action',         'name' => 'action',         'title' => __('admin.common.actions'),     'orderable' => false, 'searchable' => false, 'className' => 'text-end'],
                ];
            @endphp
            <table class="table table-hover align-middle js-datatable w-100"
                   data-url="{{ $datatableUrl }}"
                   data-columns='{{ json_encode($columns) }}'
                   data-page-length="15"
                   data-default-order='[[0,"desc"]]'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.bookings.booking_no') }}</th>
                        <th>{{ __('admin.bookings.guest') }}</th>
                        <th>{{ __('admin.nav.properties') }}</th>
                        <th>{{ __('admin.bookings.check_in') }}</th>
                        <th>{{ __('admin.bookings.check_out') }}</th>
                        <th class="text-end">{{ __('admin.bookings.total') }}</th>
                        <th>{{ __('admin.bookings.status_label') }}</th>
                        <th>{{ __('admin.bookings.payment') }}</th>
                        <th class="text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var statusInput = document.getElementById('bookings-filter-status');
    var form = document.getElementById('bookings-filter');
    document.querySelectorAll('.js-status-pill').forEach(function (btn) {
        btn.addEventListener('click', function () {
            statusInput.value = btn.dataset.filterStatus || '';
            form.submit();
        });
    });
});
</script>
@endpush
@endsection
