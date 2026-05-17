@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.availability.new'))
@section('pageTitle', __('admin.availability.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.availability_calendars.index') }}">{{ __('admin.nav.availability') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.new') }}</li>
@endsection

@section('content')
    @include('admin.availability_calendars._partials.form')
@endsection
