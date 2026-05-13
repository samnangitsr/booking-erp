@php
    /** @var bool $canShow */
    /** @var bool $canEdit */
    /** @var bool $canDelete */
    /** @var string $showUrl */
    /** @var string $editUrl */
    /** @var string $deleteUrl */
@endphp
<div class="btn-group action-btn-group" role="group">
    @if(!empty($canShow))
        <a href="{{ $showUrl }}" class="btn btn-sm btn-outline-info" data-i18n-title="admin.common.show" title="{{ __('admin.common.show') }}">
            <i class="bi bi-eye"></i>
        </a>
    @endif
    @if(!empty($canEdit))
        <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-primary" data-i18n-title="admin.common.edit" title="{{ __('admin.common.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    @if(!empty($canDelete))
        <form action="{{ $deleteUrl }}" method="POST" class="d-inline js-delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" data-i18n-title="admin.common.delete" title="{{ __('admin.common.delete') }}">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>
