@php
    $tone = match ($status ?? null) {
        'active' => 'success',
        'inactive' => 'secondary',
        default => 'secondary',
    };
@endphp
<span class="badge bg-{{ $tone }}-subtle text-{{ $tone }}-emphasis"
      data-i18n="admin.rate_plans.status_value.{{ $status }}">
    {{ __('admin.rate_plans.status_value.'.$status) }}
</span>
