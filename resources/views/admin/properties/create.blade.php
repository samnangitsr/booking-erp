@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.properties.new'))
@section('pageTitle', __('admin.properties.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.properties.index') }}">{{ __('admin.nav.properties') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.create') }}</li>
@endsection

@section('content')
    @include('admin.properties._partials.form')
@endsection
