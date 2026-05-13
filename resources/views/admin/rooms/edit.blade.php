@extends('admin.layouts.admin_layout')

@section('pageHeading', $room->room_number)
@section('pageTitle', $room->room_number)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">{{ __('admin.nav.rooms') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.show', $room->id) }}">{{ $room->room_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
    @include('admin.rooms._partials.form')
@endsection
