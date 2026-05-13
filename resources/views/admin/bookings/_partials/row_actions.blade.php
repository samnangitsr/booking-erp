@php
    /** @var \App\Models\Booking $booking */
    /** @var bool $canEdit */
    /** @var bool $canDelete */
@endphp
<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-info"
       data-i18n-title="admin.common.show" title="{{ __('admin.common.show') }}">
        <i class="bi bi-eye"></i>
    </a>
    @if($canEdit)
        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-outline-primary"
           data-i18n-title="admin.common.edit" title="{{ __('admin.common.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    @if($canDelete)
        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline js-delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"
                    data-i18n-title="admin.common.delete" title="{{ __('admin.common.delete') }}">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>
