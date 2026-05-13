@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.room_types.new'))
@section('pageTitle', __('admin.room_types.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.room_types.index') }}">{{ __('admin.nav.room_types') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.create') }}</li>
@endsection

@section('content')
    @include('admin.room_types._partials.form')
@endsection
