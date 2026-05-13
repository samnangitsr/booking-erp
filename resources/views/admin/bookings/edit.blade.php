@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.bookings.edit', ['no' => $booking->booking_no]))
@section('pageTitle', __('admin.bookings.edit', ['no' => $booking->booking_no]))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('admin.nav.bookings') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.show', $booking->id) }}">{{ $booking->booking_no }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($formMethod !== 'POST') @method($formMethod) @endif

    @include('admin.bookings._partials.form', ['booking' => $booking, 'items' => collect($items), 'options' => $options])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('admin.common.cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>{{ __('admin.common.save') }}
        </button>
    </div>
</form>
@endsection
