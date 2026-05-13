@extends('admin.layouts.admin_layout')

@section('pageHeading', $roomType->name)
@section('pageTitle', $roomType->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.room_types.index') }}">{{ __('admin.nav.room_types') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.room_types.show', $roomType->id) }}">{{ $roomType->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
    @include('admin.room_types._partials.form')
@endsection
