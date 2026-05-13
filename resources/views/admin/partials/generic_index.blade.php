@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.nav.'.$heading))
@section('pageTitle', __('admin.nav.'.$heading))

@section('breadcrumb_items')
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.nav.'.$heading) }}</li>
@endsection

@section('toolbar')
    <a href="{{ $createUrl }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('admin.common.add') }}
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover w-100 js-datatable"
                   data-url="{{ $datatableUrl }}"
                   data-columns='@json($columns)'
                   data-page-length="15">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ $col['title'] ?? $col['data'] }}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
