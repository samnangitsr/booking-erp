@extends('admin.layouts.admin_layout')

@section('pageHeading', __('admin.common.edit') . ' — ' . __('admin.nav.'.$heading))
@section('pageTitle', __('admin.common.edit') . ' — ' . __('admin.nav.'.$heading))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">{{ __('admin.nav.'.$heading) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.edit') }} #{{ $item->getKey() }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            @method($formMethod)

            @includeIf('admin.' . $heading . '._form', ['item' => $item, 'options' => $options])
            @unless(view()->exists('admin.' . $heading . '._form'))
                @include('admin.partials._generic_form_fields', ['item' => $item])
            @endunless

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">{{ __('admin.common.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('admin.common.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
