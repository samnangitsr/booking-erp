<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Property;
use App\Models\RatePlan;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class BookingController extends BaseCrudController
{
    protected string $model = Booking::class;

    protected string $routeName = 'bookings';

    protected string $viewName = 'bookings';

    protected string $permissionModule = 'bookings';

    protected string $singularLabel = 'bookings';

    protected string $headingKey = 'bookings';

    protected array $with = ['customer', 'property', 'branch'];

    /* ------------------------------------------------------------------ */
    /* INDEX — stats, filter pills, search, Yajra DataTable */
    /* ------------------------------------------------------------------ */

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        $now = Carbon::today();

        $base = Booking::query()->whereNull('deleted_at');
        $stats = [
            'pending' => (clone $base)->where('booking_status', 'pending')->count(),
            'confirmed' => (clone $base)->where('booking_status', 'confirmed')->count(),
            'checked_in' => (clone $base)->where('booking_status', 'checked_in')->count(),
            'arrivals_today' => (clone $base)->whereDate('check_in_date', $now)
                ->whereIn('booking_status', ['confirmed', 'pending'])->count(),
            'departures_today' => (clone $base)->whereDate('check_out_date', $now)
                ->whereIn('booking_status', ['checked_in', 'confirmed'])->count(),
            'revenue_paid' => (float) (clone $base)->where('payment_status', 'paid')->sum('grand_total'),
            'revenue_due' => (float) (clone $base)->sum('due_amount'),
        ];

        return view('admin.bookings.index', [
            'stats' => $stats,
            'datatableUrl' => $request->fullUrlWithQuery(['datatable' => 1]),
            'createUrl' => route('admin.bookings.create'),
            'filters' => [
                'status' => $request->input('status'),
                'payment_status' => $request->input('payment_status'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'customer_id' => $request->input('customer_id'),
                'property_id' => $request->input('property_id'),
            ],
            'customers' => Customer::query()->orderBy('first_name')->limit(50)->get(),
            'properties' => Property::query()->orderBy('name')->limit(50)->get(),
            'heading' => $this->headingKey,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = Booking::query()
            ->with(['customer:id,first_name,last_name,phone', 'property:id,name', 'branch:id,name'])
            ->select('bookings.*');

        if ($status = $request->input('status')) {
            $query->where('booking_status', $status);
        }
        if ($payment = $request->input('payment_status')) {
            $query->where('payment_status', $payment);
        }
        if ($from = $request->input('date_from')) {
            $query->whereDate('check_in_date', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('check_out_date', '<=', $to);
        }
        if ($cid = $request->input('customer_id')) {
            $query->where('customer_id', $cid);
        }
        if ($pid = $request->input('property_id')) {
            $query->where('property_id', $pid);
        }

        return DataTables::of($query)
            ->editColumn('booking_no', function (Booking $row) {
                $url = route('admin.bookings.show', $row->id);

                return '<a href="'.e($url).'" class="fw-semibold text-primary">'.e($row->booking_no).'</a>';
            })
            ->editColumn('check_in_date', function (Booking $row) {
                return optional($row->check_in_date)->format('Y-m-d');
            })
            ->editColumn('check_out_date', function (Booking $row) {
                return optional($row->check_out_date)->format('Y-m-d');
            })
            ->addColumn('customer_name', function (Booking $row) {
                if (! $row->customer) {
                    return '<span class="text-muted">—</span>';
                }
                $name = e(trim(($row->customer->first_name ?? '').' '.($row->customer->last_name ?? '')));
                $phone = $row->customer->phone ? '<div class="small text-muted">'.e($row->customer->phone).'</div>' : '';

                return '<div>'.$name.$phone.'</div>';
            })
            ->addColumn('property_name', function (Booking $row) {
                return $row->property?->name ?? '—';
            })
            ->editColumn('grand_total', function (Booking $row) {
                return '<span class="fw-semibold">'.number_format((float) $row->grand_total, 2).'</span> '
                    .'<span class="text-muted small">'.e($row->currency_code).'</span>';
            })
            ->editColumn('booking_status', function (Booking $row) {
                return view('admin.bookings._partials.status_badge', [
                    'status' => $row->booking_status,
                    'type' => 'booking',
                ])->render();
            })
            ->editColumn('payment_status', function (Booking $row) {
                return view('admin.bookings._partials.status_badge', [
                    'status' => $row->payment_status,
                    'type' => 'payment',
                ])->render();
            })
            ->addColumn('action', function (Booking $row) {
                return view('admin.bookings._partials.row_actions', [
                    'booking' => $row,
                    'canEdit' => $this->userCan('edit'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['booking_no', 'customer_name', 'grand_total', 'booking_status', 'payment_status', 'action'])
            ->toJson();
    }

    /* ------------------------------------------------------------------ */
    /* CREATE / STORE */
    /* ------------------------------------------------------------------ */

    public function create(): View
    {
        $this->authorizeAbility('create');

        $booking = new Booking([
            'booking_date' => now(),
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'total_adults' => 1,
            'total_children' => 0,
            'total_rooms' => 1,
            'nights' => 1,
            'currency_code' => 'USD',
            'booking_source' => 'admin',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return view('admin.bookings.create', [
            'booking' => $booking,
            'items' => [],
            'formAction' => route('admin.bookings.store'),
            'formMethod' => 'POST',
            'options' => $this->formOptions($booking),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateBooking($request);

        $booking = DB::transaction(function () use ($data, $request) {
            $b = new Booking($data['booking']);
            $b->booking_no = $this->generateBookingNo();
            $b->branch_id = $b->branch_id ?: session('current_branch_id');
            $b->created_by = $request->user()?->id;
            $b->booking_date = $b->booking_date ?: now();
            $b->save();

            $this->syncItems($b, $data['items']);
            $this->recomputeTotals($b);

            $this->logStatusChange($b, null, $b->booking_status, 'Booking created', $request->user()?->id);

            return $b;
        });

        flash()->success(__('admin.bookings.created'));

        return redirect()->route('admin.bookings.show', $booking->id);
    }

    /* ------------------------------------------------------------------ */
    /* SHOW */
    /* ------------------------------------------------------------------ */

    public function show(int|string $id): View
    {
        $this->authorizeAbility('view');

        $booking = Booking::query()
            ->with([
                'customer',
                'property',
                'branch',
                'creator',
                'items.roomType',
                'items.ratePlan',
                'items.room',
                'payments',
                'statusHistory.changer',
            ])
            ->findOrFail($id);

        return view('admin.bookings.show', [
            'booking' => $booking,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* EDIT / UPDATE */
    /* ------------------------------------------------------------------ */

    public function edit(int|string $id): View
    {
        $this->authorizeAbility('edit');

        $booking = Booking::with(['items'])->findOrFail($id);

        return view('admin.bookings.edit', [
            'booking' => $booking,
            'items' => $booking->items,
            'formAction' => route('admin.bookings.update', $booking->id),
            'formMethod' => 'PUT',
            'options' => $this->formOptions($booking),
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $booking = Booking::with(['items'])->findOrFail($id);
        $data = $this->validateBooking($request, $booking);

        DB::transaction(function () use ($booking, $data) {
            $booking->fill($data['booking']);
            $booking->save();
            $this->syncItems($booking, $data['items']);
            $this->recomputeTotals($booking);
        });

        flash()->success(__('admin.bookings.updated'));

        return redirect()->route('admin.bookings.show', $booking->id);
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        $booking = Booking::findOrFail($id);
        $booking->delete();
        flash()->success(__('admin.bookings.deleted'));

        return redirect()->route('admin.bookings.index');
    }

    /* ------------------------------------------------------------------ */
    /* LIFECYCLE ACTIONS */
    /* ------------------------------------------------------------------ */

    public function confirm(Request $request, int $id): RedirectResponse
    {
        return $this->transition($request, $id, 'confirmed', 'edit');
    }

    public function checkIn(Request $request, int $id): RedirectResponse
    {
        return $this->transition($request, $id, 'checked_in', 'edit');
    }

    public function checkOut(Request $request, int $id): RedirectResponse
    {
        return $this->transition($request, $id, 'checked_out', 'edit');
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        return $this->transition($request, $id, 'cancelled', 'edit', true);
    }

    private function transition(Request $request, int $id, string $target, string $ability, bool $isCancel = false): RedirectResponse
    {
        $this->authorizeAbility($ability);

        $booking = Booking::findOrFail($id);
        $old = $booking->booking_status;
        $note = $request->input('note');

        $booking->booking_status = $target;
        if ($isCancel) {
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $note;
        }
        $booking->save();

        $this->logStatusChange($booking, $old, $target, $note, $request->user()?->id);

        flash()->success(__('admin.bookings.status_changed', [
            'status' => __('admin.bookings.status.'.$target),
        ]));

        return redirect()->route('admin.bookings.show', $booking->id);
    }

    /* ------------------------------------------------------------------ */
    /* AJAX cascading: room types for a property */
    /* ------------------------------------------------------------------ */

    public function roomTypesForProperty(Request $request): JsonResponse
    {
        $propertyId = (int) $request->input('property_id');
        $rows = RoomType::query()
            ->when($propertyId, fn ($q) => $q->where('property_id', $propertyId))
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'base_price', 'max_adults', 'max_children']);

        return response()->json([
            'data' => $rows->map(fn ($r) => [
                'id' => $r->id,
                'text' => $r->name,
                'base_price' => (float) $r->base_price,
                'max_adults' => $r->max_adults,
                'max_children' => $r->max_children,
            ]),
        ]);
    }

    public function ratePlansForRoomType(Request $request): JsonResponse
    {
        $roomTypeId = (int) $request->input('room_type_id');
        $rows = RatePlan::query()
            ->when($roomTypeId, fn ($q) => $q->where('room_type_id', $roomTypeId))
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'meal_plan']);

        return response()->json([
            'data' => $rows->map(fn ($r) => [
                'id' => $r->id,
                'text' => $r->name,
                'meal_plan' => $r->meal_plan,
            ]),
        ]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q'));
        $rows = Customer::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);

        return response()->json([
            'data' => $rows->map(fn ($c) => [
                'id' => $c->id,
                'text' => trim($c->first_name.' '.$c->last_name).($c->phone ? ' — '.$c->phone : ''),
                'phone' => $c->phone,
                'email' => $c->email,
            ]),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* HELPERS */
    /* ------------------------------------------------------------------ */

    protected function formOptions(?Model $booking): array
    {
        return [
            'customers' => Customer::query()->orderBy('first_name')->limit(50)->get(),
            'properties' => Property::query()->orderBy('name')->limit(50)->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'roomTypes' => $booking && $booking->property_id
                ? RoomType::query()->where('property_id', $booking->property_id)->orderBy('name')->get()
                : collect(),
            'statuses' => Booking::STATUSES,
            'paymentStatuses' => Booking::PAYMENT_STATUSES,
            'sources' => Booking::SOURCES,
            'customerSearchUrl' => route('admin.bookings.customers.search'),
            'roomTypesUrl' => route('admin.bookings.room_types'),
            'ratePlansUrl' => route('admin.bookings.rate_plans'),
        ];
    }

    private function validateBooking(Request $request, ?Booking $booking = null): array
    {
        $rules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after_or_equal:check_in_date'],
            'total_adults' => ['required', 'integer', 'min:1'],
            'total_children' => ['nullable', 'integer', 'min:0'],
            'booking_source' => ['required', 'in:'.implode(',', Booking::SOURCES)],
            'booking_status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
            'payment_status' => ['required', 'in:'.implode(',', Booking::PAYMENT_STATUSES)],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'fee_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'items.*.rate_plan_id' => ['nullable', 'integer', 'exists:rate_plans,id'],
            'items.*.rooms_count' => ['required', 'integer', 'min:1'],
            'items.*.adults' => ['required', 'integer', 'min:1'],
            'items.*.children' => ['nullable', 'integer', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        $request->validate($rules);

        $bookingFields = $request->only([
            'company_id', 'branch_id', 'customer_id', 'property_id',
            'check_in_date', 'check_out_date',
            'total_adults', 'total_children',
            'discount_amount', 'tax_amount', 'fee_amount', 'paid_amount',
            'currency_code', 'booking_source', 'payment_status', 'booking_status',
            'special_request',
        ]);

        return [
            'booking' => $bookingFields,
            'items' => $request->input('items', []),
        ];
    }

    private function syncItems(Booking $booking, array $itemsInput): void
    {
        $kept = [];

        foreach ($itemsInput as $row) {
            $checkIn = $row['check_in_date'] ?? $booking->check_in_date->format('Y-m-d');
            $checkOut = $row['check_out_date'] ?? $booking->check_out_date->format('Y-m-d');
            $nights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
            $roomsCount = (int) ($row['rooms_count'] ?? 1);
            $unitPrice = (float) ($row['unit_price'] ?? 0);
            $totalPrice = $unitPrice * $nights * $roomsCount;

            $roomType = RoomType::find($row['room_type_id'] ?? null);
            $ratePlan = ! empty($row['rate_plan_id']) ? RatePlan::find($row['rate_plan_id']) : null;

            $attrs = [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'room_type_id' => $row['room_type_id'],
                'rate_plan_id' => $row['rate_plan_id'] ?? null,
                'room_id' => $row['room_id'] ?? null,
                'room_name' => $roomType?->name ?? ('Room '.$row['room_type_id']),
                'rate_plan_name' => $ratePlan?->name,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'nights' => $nights,
                'rooms_count' => $roomsCount,
                'adults' => (int) ($row['adults'] ?? 1),
                'children' => (int) ($row['children'] ?? 0),
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'status' => 'reserved',
            ];

            if (! empty($row['id'])) {
                $item = $booking->items()->where('id', $row['id'])->first();
                if ($item) {
                    $item->fill($attrs)->save();
                    $kept[] = $item->id;

                    continue;
                }
            }
            $item = $booking->items()->create($attrs);
            $kept[] = $item->id;
        }

        // Remove any items not in the submitted list (edit flow).
        $booking->items()->whereNotIn('id', $kept ?: [0])->delete();
    }

    private function recomputeTotals(Booking $booking): void
    {
        $booking->loadMissing('items');
        $subtotal = (float) $booking->items->sum('total_price');
        $checkIn = Carbon::parse($booking->check_in_date);
        $checkOut = Carbon::parse($booking->check_out_date);
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $totalRooms = (int) $booking->items->sum('rooms_count');

        $discount = (float) ($booking->discount_amount ?? 0);
        $tax = (float) ($booking->tax_amount ?? 0);
        $fee = (float) ($booking->fee_amount ?? 0);
        $grand = max(0, $subtotal - $discount + $tax + $fee);
        $paid = (float) ($booking->paid_amount ?? 0);
        $due = max(0, $grand - $paid);

        $booking->subtotal = $subtotal;
        $booking->grand_total = $grand;
        $booking->due_amount = $due;
        $booking->nights = $nights;
        $booking->total_rooms = $totalRooms ?: 1;
        // payment_status auto-derivation: keep manual unless clearly out of sync.
        if ($paid <= 0) {
            $booking->payment_status = 'unpaid';
        } elseif ($paid >= $grand && $grand > 0) {
            $booking->payment_status = 'paid';
        } else {
            $booking->payment_status = 'partial';
        }
        $booking->save();
    }

    private function logStatusChange(Booking $booking, ?string $old, string $new, ?string $note, ?int $userId): void
    {
        BookingStatusHistory::create([
            'booking_id' => $booking->id,
            'old_status' => $old,
            'new_status' => $new,
            'note' => $note,
            'changed_by' => $userId,
        ]);
    }

    private function generateBookingNo(): string
    {
        $prefix = 'BK';
        $date = now()->format('ymd');
        $rand = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        return $prefix.$date.$rand;
    }
}
