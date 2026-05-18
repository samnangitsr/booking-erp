@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.rate_plans.new'))
@section('pageTitle', __('admin.rate_plans.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rate_plans.index') }}">{{ __('admin.nav.rate_plans') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.create') }}</li>
@endsection

@section('content')
    @include('admin.rate_plans._partials.form')
@endsection
