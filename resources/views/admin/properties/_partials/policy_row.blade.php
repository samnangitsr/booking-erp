@php
    $p = $policy ?? null;
    $idx = $index;
@endphp
<div class="row g-2 align-items-end mb-2 border-bottom pb-2" data-row>
    <input type="hidden" name="policies[{{ $idx }}][id]" value="{{ $p->id ?? '' }}">
    <div class="col-md-3">
        <label class="form-label small mb-1" data-i18n="admin.properties.policy_type">{{ __('admin.properties.policy_type') }}</label>
        <select name="policies[{{ $idx }}][policy_type]" class="form-select form-select-sm">
            @foreach($policyTypes as $pt)
                <option value="{{ $pt }}" @selected(($p->policy_type ?? '') === $pt)>{{ Str::title(str_replace('_', ' ', $pt)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label small mb-1" data-i18n="admin.properties.policy_title">{{ __('admin.properties.policy_title') }}</label>
        <input type="text" class="form-control form-control-sm" name="policies[{{ $idx }}][title]" value="{{ $p->title ?? '' }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small mb-1" data-i18n="admin.properties.policy_description">{{ __('admin.properties.policy_description') }}</label>
        <input type="text" class="form-control form-control-sm" name="policies[{{ $idx }}][description]" value="{{ $p->description ?? '' }}">
    </div>
    <div class="col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-row">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>
