@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.rooms.new'))
@section('pageTitle', __('admin.rooms.new'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">{{ __('admin.nav.rooms') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.create') }}</li>
@endsection

@section('content')
    @include('admin.rooms._partials.form')
@endsection
