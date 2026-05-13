@php
    /** @var string $showUrl */
    /** @var string $editUrl */
    /** @var string $deleteUrl */
    /** @var bool $canShow */
    /** @var bool $canEdit */
    /** @var bool $canDelete */
@endphp
<div class="btn-group btn-group-sm" role="group">
    @if($canShow ?? true)
        <a href="{{ $showUrl }}" class="btn btn-outline-info" title="{{ __('admin.common.show') }}">
            <i class="bi bi-eye"></i>
        </a>
    @endif
    @if($canEdit ?? true)
        <a href="{{ $editUrl }}" class="btn btn-outline-primary" title="{{ __('admin.common.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    @if($canDelete ?? true)
        <form action="{{ $deleteUrl }}" method="POST" class="d-inline js-delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" title="{{ __('admin.common.delete') }}">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>
