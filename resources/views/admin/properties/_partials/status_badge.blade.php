@php
    /** @var string|null $status */
    /** @var string|null $type */
    $type = $type ?? 'status';
    $palette = [
        'status' => [
            'active'    => ['bg-success-subtle text-success-emphasis border border-success-subtle', 'check2-circle'],
            'inactive'  => ['bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', 'pause-circle'],
            'suspended' => ['bg-danger-subtle text-danger-emphasis border border-danger-subtle', 'slash-circle'],
        ],
        'approval' => [
            'pending'  => ['bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'hourglass-split'],
            'approved' => ['bg-success-subtle text-success-emphasis border border-success-subtle', 'patch-check-fill'],
            'rejected' => ['bg-danger-subtle text-danger-emphasis border border-danger-subtle', 'x-circle'],
        ],
        'room' => [
            'available'   => ['bg-success-subtle text-success-emphasis border border-success-subtle', 'door-open'],
            'occupied'    => ['bg-primary-subtle text-primary-emphasis border border-primary-subtle', 'person-fill'],
            'maintenance' => ['bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'tools'],
            'inactive'    => ['bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', 'slash-circle'],
        ],
    ];

    $entries = $palette[$type] ?? [];
    [$classes, $icon] = $entries[$status] ?? ['bg-light text-muted border', 'tag'];
    $key = match ($type) {
        'approval' => 'admin.properties.approval_status.'.$status,
        'room' => 'admin.rooms.status.'.$status,
        default => 'admin.properties.status_value.'.$status,
    };
    $label = $status ? __($key) : '—';
@endphp
<span class="badge rounded-pill px-2 py-1 fw-medium {{ $classes }}" data-i18n="{{ $key }}">
    <i class="bi bi-{{ $icon }} me-1"></i>{{ $label }}
</span>
