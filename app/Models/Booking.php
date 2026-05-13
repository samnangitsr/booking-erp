<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'property_id',
        'booking_no',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'nights',
        'total_rooms',
        'total_adults',
        'total_children',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'fee_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'currency_code',
        'booking_source',
        'payment_status',
        'booking_status',
        'special_request',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'customer_id' => 'integer',
        'property_id' => 'integer',
        'booking_date' => 'datetime',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'nights' => 'integer',
        'total_rooms' => 'integer',
        'total_adults' => 'integer',
        'total_children' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'created_by' => 'integer',
    ];

    public const STATUSES = [
        'pending',
        'confirmed',
        'checked_in',
        'checked_out',
        'cancelled',
        'no_show',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid',
        'partial',
        'paid',
        'refunded',
    ];

    public const SOURCES = [
        'website',
        'mobile_app',
        'admin',
        'partner_api',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class)->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('payment_date');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class)->orderByDesc('id');
    }
}
