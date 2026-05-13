@extends('admin.layouts.admin_layout')

@section('pageHeading', $property->name)
@section('pageTitle', $property->name)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.properties.index') }}">{{ __('admin.nav.properties') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.properties.show', $property->id) }}">{{ $property->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }}</li>
@endsection

@section('content')
    @include('admin.properties._partials.form')
@endsection
