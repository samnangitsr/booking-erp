@extends('admin.layouts.admin_layout')

@section('pageHeading', $booking->booking_no)
@section('pageTitle', $booking->booking_no)

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('admin.nav.bookings') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $booking->booking_no }}</li>
@endsection

@section('content')
@php
    $statusTransitions = [
        'pending'     => ['confirm', 'cancel'],
        'confirmed'   => ['check_in', 'cancel'],
        'checked_in'  => ['check_out', 'cancel'],
        'checked_out' => [],
        'cancelled'   => [],
        'no_show'     => [],
    ];
    $available = $statusTransitions[$booking->booking_status] ?? [];
    $currency = $booking->currency_code ?: 'USD';
    $customer = $booking->customer;
    $property = $booking->property;
    $branch   = $booking->branch;
@endphp

<div class="card shadow-sm border-0 mb-3 booking-hero">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div>
                <div class="small text-muted text-uppercase">{{ __('admin.bookings.booking_no') }}</div>
                <h3 class="mb-1 fw-bold">{{ $booking->booking_no }}</h3>
                <div class="d-flex gap-2 flex-wrap">
                    @include('admin.bookings._partials.status_badge', ['status' => $booking->booking_status, 'type' => 'booking'])
                    @include('admin.bookings._partials.status_badge', ['status' => $booking->payment_status, 'type' => 'payment'])
                    @if($booking->booking_source)
                        <span class="badge bg-light text-muted border">
                            <i class="bi bi-globe2 me-1"></i>{{ __('admin.bookings.source_label.'.$booking->booking_source) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="ms-auto d-flex flex-wrap gap-2 align-items-center">
                @if(in_array('confirm', $available))
                    <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-check2-circle me-1"></i>{{ __('admin.bookings.actions.confirm') }}
                        </button>
                    </form>
                @endif
                @if(in_array('check_in', $available))
                    <form action="{{ route('admin.bookings.check_in', $booking->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('admin.bookings.actions.check_in') }}
                        </button>
                    </form>
                @endif
                @if(in_array('check_out', $available))
                    <form action="{{ route('admin.bookings.check_out', $booking->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-box-arrow-right me-1"></i>{{ __('admin.bookings.actions.check_out') }}
                        </button>
                    </form>
                @endif
                @if(in_array('cancel', $available))
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancel-booking-modal">
                        <i class="bi bi-x-circle me-1"></i>{{ __('admin.bookings.actions.cancel') }}
                    </button>
                @endif
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>{{ __('admin.common.edit') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- LEFT column --}}
    <div class="col-lg-8">
        {{-- Guest + Stay summary --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-person-vcard text-primary me-1"></i>{{ __('admin.bookings.section.guest') }}</h6>
                    </div>
                    <div class="card-body">
                        @if($customer)
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                     style="width:48px;height:48px;font-weight:600;">
                                    {{ strtoupper(mb_substr($customer->first_name ?? '?', 0, 1).mb_substr($customer->last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ trim($customer->first_name.' '.$customer->last_name) }}</div>
                                    <div class="small text-muted">{{ $customer->customer_code }}</div>
                                </div>
                            </div>
                            <dl class="row mb-0 small">
                                @if($customer->phone)
                                    <dt class="col-4 text-muted">{{ __('admin.common.phone') }}</dt>
                                    <dd class="col-8">{{ $customer->phone }}</dd>
                                @endif
                                @if($customer->email)
                                    <dt class="col-4 text-muted">{{ __('admin.common.email') }}</dt>
                                    <dd class="col-8">{{ $customer->email }}</dd>
                                @endif
                                @if($customer->nationality)
                                    <dt class="col-4 text-muted">{{ __('admin.bookings.nationality') }}</dt>
                                    <dd class="col-8">{{ $customer->nationality }}</dd>
                                @endif
                            </dl>
                        @else
                            <p class="text-muted mb-0">—</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-calendar3 text-primary me-1"></i>{{ __('admin.bookings.section.stay') }}</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0 small">
                            <dt class="col-5 text-muted">{{ __('admin.nav.properties') }}</dt>
                            <dd class="col-7 fw-medium">{{ $property?->name ?? '—' }}</dd>
                            @if($branch)
                                <dt class="col-5 text-muted">{{ __('admin.nav.branches') }}</dt>
                                <dd class="col-7">{{ $branch->name }}</dd>
                            @endif
                            <dt class="col-5 text-muted">{{ __('admin.bookings.check_in') }}</dt>
                            <dd class="col-7">{{ optional($booking->check_in_date)->format('D, M j, Y') }}</dd>
                            <dt class="col-5 text-muted">{{ __('admin.bookings.check_out') }}</dt>
                            <dd class="col-7">{{ optional($booking->check_out_date)->format('D, M j, Y') }}</dd>
                            <dt class="col-5 text-muted">{{ __('admin.bookings.nights') }}</dt>
                            <dd class="col-7"><span class="badge bg-primary-subtle text-primary-emphasis">{{ $booking->nights }}</span></dd>
                            <dt class="col-5 text-muted">{{ __('admin.bookings.guests') }}</dt>
                            <dd class="col-7">{{ $booking->total_adults }} {{ __('admin.bookings.adults') }} · {{ $booking->total_children }} {{ __('admin.bookings.children') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking items --}}
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-door-closed text-primary me-1"></i>{{ __('admin.bookings.section.items') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>{{ __('admin.nav.room_types') }}</th>
                                <th>{{ __('admin.nav.rate_plans') }}</th>
                                <th class="text-center">{{ __('admin.bookings.guests') }}</th>
                                <th class="text-center">{{ __('admin.bookings.nights') }}</th>
                                <th class="text-end">{{ __('admin.bookings.unit_price') }}</th>
                                <th class="text-end pe-3">{{ __('admin.bookings.line_total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->items as $i => $item)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->room_name }}</div>
                                        <div class="small text-muted">
                                            {{ $item->rooms_count }} × {{ optional($item->roomType)->name ?? '—' }}
                                        </div>
                                    </td>
                                    <td>{{ $item->rate_plan_name ?? '—' }}</td>
                                    <td class="text-center">{{ $item->adults }} / {{ $item->children }}</td>
                                    <td class="text-center">{{ $item->nights }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end pe-3 fw-semibold">{{ number_format($item->total_price, 2) }} <small class="text-muted">{{ $currency }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">{{ __('admin.common.no_records') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payments --}}
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-credit-card text-primary me-1"></i>{{ __('admin.nav.payments') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">{{ __('admin.bookings.payment_no') }}</th>
                                <th>{{ __('admin.bookings.date') }}</th>
                                <th>{{ __('admin.bookings.method') }}</th>
                                <th class="text-end">{{ __('admin.bookings.amount') }}</th>
                                <th>{{ __('admin.common.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->payments as $payment)
                                <tr>
                                    <td class="ps-3">{{ $payment->payment_no }}</td>
                                    <td>{{ optional($payment->payment_date)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $payment->payment_gateway ?? '—' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($payment->amount, 2) }} <small class="text-muted">{{ $payment->currency_code ?? $currency }}</small></td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success-emphasis">{{ $payment->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">{{ __('admin.bookings.no_payments') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($booking->special_request)
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-square-text text-primary me-1"></i>{{ __('admin.bookings.special_request') }}</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-pre-wrap" style="white-space: pre-wrap;">{{ $booking->special_request }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- RIGHT column --}}
    <div class="col-lg-4">
        {{-- Pricing breakdown --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-receipt text-primary me-1"></i>{{ __('admin.bookings.section.pricing') }}</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted">{{ __('admin.bookings.subtotal') }}</dt>
                    <dd class="col-6 text-end">{{ number_format($booking->subtotal, 2) }}</dd>
                    @if((float) $booking->discount_amount > 0)
                        <dt class="col-6 text-muted">{{ __('admin.bookings.discount') }}</dt>
                        <dd class="col-6 text-end text-success">- {{ number_format($booking->discount_amount, 2) }}</dd>
                    @endif
                    @if((float) $booking->tax_amount > 0)
                        <dt class="col-6 text-muted">{{ __('admin.bookings.tax') }}</dt>
                        <dd class="col-6 text-end">{{ number_format($booking->tax_amount, 2) }}</dd>
                    @endif
                    @if((float) $booking->fee_amount > 0)
                        <dt class="col-6 text-muted">{{ __('admin.bookings.fees') }}</dt>
                        <dd class="col-6 text-end">{{ number_format($booking->fee_amount, 2) }}</dd>
                    @endif
                    <dt class="col-6 fw-bold border-top pt-2 text-primary">{{ __('admin.bookings.grand_total') }}</dt>
                    <dd class="col-6 text-end fs-5 fw-bold border-top pt-2 text-primary">{{ number_format($booking->grand_total, 2) }} <small class="text-muted">{{ $currency }}</small></dd>
                    <dt class="col-6 text-muted">{{ __('admin.bookings.paid') }}</dt>
                    <dd class="col-6 text-end text-success">{{ number_format($booking->paid_amount, 2) }}</dd>
                    <dt class="col-6 text-muted fw-medium">{{ __('admin.bookings.due') }}</dt>
                    <dd class="col-6 text-end fw-bold text-danger">{{ number_format($booking->due_amount, 2) }} <small class="text-muted">{{ $currency }}</small></dd>
                </dl>
            </div>
        </div>

        {{-- Status timeline --}}
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history text-primary me-1"></i>{{ __('admin.bookings.section.timeline') }}</h6>
            </div>
            <div class="card-body">
                @if($booking->statusHistory->isEmpty())
                    <p class="text-muted small mb-0">{{ __('admin.bookings.no_history') }}</p>
                @else
                    <ol class="booking-timeline list-unstyled m-0">
                        @foreach($booking->statusHistory as $h)
                            <li class="timeline-item">
                                <div class="timeline-dot bg-{{ $loop->first ? 'primary' : 'secondary' }}"></div>
                                <div class="timeline-content">
                                    <div class="small text-muted">{{ optional($h->created_at)->diffForHumans() }} · {{ optional($h->created_at)->format('Y-m-d H:i') }}</div>
                                    <div>
                                        @if($h->old_status)
                                            <span class="badge bg-light text-muted border me-1">{{ __('admin.bookings.status.'.$h->old_status) }}</span>
                                            <i class="bi bi-arrow-right text-muted small"></i>
                                        @endif
                                        @include('admin.bookings._partials.status_badge', ['status' => $h->new_status, 'type' => 'booking'])
                                    </div>
                                    @if($h->note)
                                        <div class="small text-muted mt-1 fst-italic">"{{ $h->note }}"</div>
                                    @endif
                                    @if($h->changer)
                                        <div class="small text-muted">{{ __('admin.bookings.by') }} {{ $h->changer->name }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body small text-muted">
                <div class="d-flex justify-content-between">
                    <span>{{ __('admin.bookings.created_at') }}</span>
                    <span>{{ optional($booking->created_at)->format('Y-m-d H:i') }}</span>
                </div>
                @if($booking->creator)
                    <div class="d-flex justify-content-between">
                        <span>{{ __('admin.bookings.created_by') }}</span>
                        <span>{{ $booking->creator->name }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    <span>{{ __('admin.common.updated_at') }}</span>
                    <span>{{ optional($booking->updated_at)->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel modal --}}
<div class="modal fade" id="cancel-booking-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.bookings.cancel', $booking->id) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-1"></i>{{ __('admin.bookings.actions.cancel') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">{{ __('admin.bookings.cancel_confirm', ['no' => $booking->booking_no]) }}</p>
                <div class="mb-3">
                    <label for="cancel-note" class="form-label">{{ __('admin.bookings.cancel_reason') }}</label>
                    <textarea id="cancel-note" name="note" rows="3" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i>{{ __('admin.bookings.actions.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.booking-hero { border-left: 4px solid var(--bs-primary); }
.booking-timeline { position: relative; padding-left: 1.25rem; }
.booking-timeline::before {
    content: ''; position: absolute; left: 6px; top: 4px; bottom: 4px;
    width: 2px; background: var(--bs-border-color);
}
.timeline-item { position: relative; padding-bottom: 1rem; }
.timeline-dot {
    position: absolute; left: -1.25rem; top: 4px;
    width: 14px; height: 14px; border-radius: 50%;
    border: 2px solid var(--bs-body-bg);
    box-shadow: 0 0 0 2px var(--bs-border-color);
}
.timeline-content { padding-left: .25rem; }
.timeline-item:last-child { padding-bottom: 0; }
</style>
@endpush
@endsection
