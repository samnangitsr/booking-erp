@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.common.show') . ' — ' . __('admin.nav.'.$heading))
@section('pageTitle', __('admin.common.show') . ' — ' . __('admin.nav.'.$heading))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('admin.nav.'.$heading) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.show') }} #{{ $item->getKey() }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            @foreach($item->getAttributes() as $key => $value)
                <dt class="col-sm-3 text-muted small">{{ $key }}</dt>
                <dd class="col-sm-9">{{ is_scalar($value) ? $value : json_encode($value) }}</dd>
            @endforeach
        </dl>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">{{ __('admin.common.back') }}</a>
        </div>
    </div>
</div>
@endsection
