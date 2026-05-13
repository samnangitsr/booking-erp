@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.rooms.title').' '.$room->room_number)
@section('pageTitle', __('admin.rooms.title').' '.$room->room_number)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">{{ __('admin.nav.rooms') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $room->room_number }}</li>
@endsection

@section('toolbar')
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> {{ __('admin.common.back') }}
    </a>
    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="btn btn-outline-primary ms-2">
        <i class="bi bi-pencil"></i> {{ __('admin.common.edit') }}
    </a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="display-3 fw-bold mb-2">{{ $room->room_number }}</div>
                <div class="mb-2">
                    @include('admin.properties._partials.status_badge', ['type' => 'room', 'status' => $room->status])
                </div>
                <div class="text-muted small">
                    <i class="bi bi-layers"></i> {{ __('admin.rooms.floor') }}: <strong>{{ $room->floor ?: '—' }}</strong>
                </div>
                <hr>
                <div class="mb-1">
                    <a href="{{ route('admin.properties.show', $room->property_id) }}" class="link-primary">{{ $room->property?->name }}</a>
                </div>
                <div class="text-muted">
                    <a href="{{ route('admin.room_types.show', $room->room_type_id) }}" class="link-secondary">{{ $room->roomType?->name }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white"><strong data-i18n="admin.rooms.upcoming_bookings">{{ __('admin.rooms.upcoming_bookings') }}</strong></div>
            <div class="card-body">
                @if($upcomingBookings->isEmpty())
                    <p class="text-muted mb-0">{{ __('admin.rooms.no_upcoming') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.bookings.booking_no') }}</th>
                                    <th>{{ __('admin.bookings.guest') }}</th>
                                    <th>{{ __('admin.bookings.check_in') }}</th>
                                    <th>{{ __('admin.bookings.check_out') }}</th>
                                    <th>{{ __('admin.common.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingBookings as $b)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $b->booking_id) }}" class="fw-semibold">{{ $b->booking_no }}</a>
                                        </td>
                                        <td>{{ trim($b->first_name.' '.$b->last_name) }}</td>
                                        <td>{{ $b->check_in_date }}</td>
                                        <td>{{ $b->check_out_date }}</td>
                                        <td>@include('admin.bookings._partials.status_badge', ['type' => 'booking', 'status' => $b->booking_status])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
