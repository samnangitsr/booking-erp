@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.daily_rates.edit'))
@section('pageTitle', __('admin.daily_rates.edit'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.daily_rates.index') }}">{{ __('admin.nav.daily_rates') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
    @include('admin.daily_rates._partials.form')
@endsection
