@php
    $tone = match ($value ?? null) {
        'none' => 'secondary',
        'breakfast' => 'warning',
        'half_board' => 'info',
        'full_board' => 'primary',
        default => 'secondary',
    };
    $icon = match ($value ?? null) {
        'breakfast' => 'cup-hot',
        'half_board' => 'egg-fried',
        'full_board' => 'basket',
        default => 'dash',
    };
@endphp
<span class="badge bg-{{ $tone }}-subtle text-{{ $tone }}-emphasis"
      data-i18n="admin.rate_plans.meal_plan_value.{{ $value }}">
    <i class="bi bi-{{ $icon }} me-1"></i>{{ __('admin.rate_plans.meal_plan_value.'.$value) }}
</span>
