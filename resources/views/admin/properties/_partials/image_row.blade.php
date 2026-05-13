@php
    $img = $image ?? null;
    $idx = $index;
@endphp
<div class="row g-2 align-items-end mb-2 border-bottom pb-2" data-row>
    <input type="hidden" name="images[{{ $idx }}][id]" value="{{ $img->id ?? '' }}">
    <div class="col-md-2 col-3">
        <img src="{{ $img->image_path ?? 'https://placehold.co/120x80/e9ecef/6c757d?text=...' }}"
             alt="" class="img-fluid rounded js-image-preview" style="max-height:60px;object-fit:cover;">
    </div>
    <div class="col-md-5 col-9">
        <label class="form-label small mb-1" data-i18n="admin.properties.image_url">{{ __('admin.properties.image_url') }}</label>
        <input type="url" class="form-control form-control-sm js-image-url" name="images[{{ $idx }}][image_path]" value="{{ $img->image_path ?? '' }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-1" data-i18n="admin.properties.image_title">{{ __('admin.properties.image_title') }}</label>
        <input type="text" class="form-control form-control-sm" name="images[{{ $idx }}][title]" value="{{ $img->title ?? '' }}">
    </div>
    <div class="col-md-1 col-6">
        <div class="form-check">
            <input type="hidden" name="images[{{ $idx }}][is_cover]" value="0">
            <input type="checkbox" class="form-check-input js-cover-toggle" name="images[{{ $idx }}][is_cover]" value="1" @checked($img && $img->is_cover)>
            <label class="form-check-label small" data-i18n="admin.properties.cover">{{ __('admin.properties.cover') }}</label>
        </div>
    </div>
    <div class="col-md-1 col-6 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-row">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>
