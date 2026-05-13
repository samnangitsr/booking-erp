@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.bookings.new'))
@section('pageTitle', __('admin.bookings.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('admin.nav.bookings') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.bookings.new') }}</li>
@endsection

@section('content')
<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($formMethod !== 'POST') @method($formMethod) @endif

    @include('admin.bookings._partials.form', ['booking' => $booking, 'items' => collect($items), 'options' => $options])

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('admin.common.cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>{{ __('admin.bookings.save') }}
        </button>
    </div>
</form>
@endsection
