@extends('admin.layouts.admin_layout')

@section('pageHeading', $ratePlan->name)
@section('pageTitle', $ratePlan->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rate_plans.index') }}">{{ __('admin.nav.rate_plans') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.rate_plans.show', $ratePlan->id) }}">{{ $ratePlan->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
    @include('admin.rate_plans._partials.form')
@endsection
