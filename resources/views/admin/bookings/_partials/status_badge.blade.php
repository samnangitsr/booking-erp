@php
    /** @var string|null $status */
    /** @var string|null $type */
    $type = $type ?? 'booking';
    $palette = [
        'booking' => [
            'pending'      => ['bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'hourglass-split'],
            'confirmed'    => ['bg-info-subtle text-info-emphasis border border-info-subtle', 'check2-circle'],
            'checked_in'   => ['bg-primary-subtle text-primary-emphasis border border-primary-subtle', 'box-arrow-in-right'],
            'checked_out'  => ['bg-success-subtle text-success-emphasis border border-success-subtle', 'box-arrow-right'],
            'cancelled'    => ['bg-danger-subtle text-danger-emphasis border border-danger-subtle', 'x-circle'],
            'no_show'      => ['bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', 'slash-circle'],
        ],
        'payment' => [
            'unpaid'   => ['bg-danger-subtle text-danger-emphasis border border-danger-subtle', 'cash-coin'],
            'partial'  => ['bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'pie-chart-fill'],
            'paid'     => ['bg-success-subtle text-success-emphasis border border-success-subtle', 'check-circle-fill'],
            'refunded' => ['bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', 'arrow-counterclockwise'],
        ],
        'item' => [
            'reserved'    => ['bg-info-subtle text-info-emphasis', 'bookmark'],
            'checked_in'  => ['bg-primary-subtle text-primary-emphasis', 'box-arrow-in-right'],
            'checked_out' => ['bg-success-subtle text-success-emphasis', 'box-arrow-right'],
            'cancelled'   => ['bg-danger-subtle text-danger-emphasis', 'x-circle'],
        ],
    ];

    $entries = $palette[$type] ?? [];
    [$classes, $icon] = $entries[$status] ?? ['bg-light text-muted border', 'tag'];
    $label = $status ? __('admin.bookings.status.'.$status) : '—';
@endphp
<span class="badge rounded-pill px-2 py-1 fw-medium {{ $classes }}">
    <i class="bi bi-{{ $icon }} me-1"></i>{{ $label }}
</span>
