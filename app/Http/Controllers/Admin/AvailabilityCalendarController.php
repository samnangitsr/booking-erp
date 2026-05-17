<?php

namespace App\Http\Controllers\Admin;

use App\Models\AvailabilityCalendar;
use App\Models\Property;
use App\Models\RoomType;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvailabilityCalendarController extends BaseCrudController
{
    protected string $model = AvailabilityCalendar::class;

    protected string $routeName = 'availability_calendars';

    protected string $viewName = 'availability_calendars';

    protected string $permissionModule = 'rates';

    protected string $singularLabel = 'rates';

    protected string $headingKey = 'admin.nav.availability';

    protected const GRID_DAYS = 14;

    public function index(Request $request): View
    {
        $this->authorizeAbility('view');

        $properties = Property::orderBy('name')->get(['id', 'name']);
        $propertyId = $request->integer('property_id') ?: ($properties->first()->id ?? null);

        $start = $this->parseStart($request->input('start'));
        $end = $start->addDays(self::GRID_DAYS - 1);

        $roomTypes = RoomType::query()
            ->when($propertyId, fn ($q) => $q->where('property_id', $propertyId))
            ->orderBy('name')
            ->get();

        $rows = AvailabilityCalendar::query()
            ->whereIn('room_type_id', $roomTypes->pluck('id'))
            ->whereBetween('available_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy(fn (AvailabilityCalendar $r) => $r->room_type_id.'|'.$r->available_date->toDateString());

        $dates = collect();
        for ($d = $start; $d->lte($end); $d = $d->addDay()) {
            $dates->push($d);
        }

        $summary = [
            'available' => 0,
            'booked' => 0,
            'blocked' => 0,
            'stop_sell' => 0,
        ];

        foreach ($rows as $bucket) {
            foreach ($bucket as $r) {
                $summary['available'] += $r->available_rooms;
                $summary['booked'] += $r->booked_rooms;
                $summary['blocked'] += $r->blocked_rooms;
                if ($r->stop_sell) {
                    $summary['stop_sell']++;
                }
            }
        }

        return view('admin.availability_calendars.index', [
            'properties' => $properties,
            'propertyId' => $propertyId,
            'roomTypes' => $roomTypes,
            'rows' => $rows,
            'dates' => $dates,
            'start' => $start,
            'end' => $end,
            'prevStart' => $start->subDays(self::GRID_DAYS),
            'nextStart' => $start->addDays(self::GRID_DAYS),
            'today' => CarbonImmutable::today(),
            'summary' => $summary,
            'heading' => $this->headingKey,
        ]);
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.availability_calendars.create', [
            'availability' => new AvailabilityCalendar(['total_rooms' => 0, 'booked_rooms' => 0, 'blocked_rooms' => 0]),
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'roomTypes' => RoomType::orderBy('name')->get(['id', 'name', 'property_id']),
            'formAction' => route('admin.availability_calendars.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateAvailability($request);
        $row = new AvailabilityCalendar($data);
        $row->recalcAvailable();
        $row->save();

        flash()->success(__('admin.availability.created'));

        return redirect()->route('admin.availability_calendars.index', [
            'property_id' => $row->property_id,
            'start' => $row->available_date->toDateString(),
        ]);
    }

    public function show(int $id): View
    {
        $this->authorizeAbility('view');

        $row = AvailabilityCalendar::with(['property', 'roomType'])->findOrFail($id);

        return view('admin.availability_calendars.show', compact('row'));
    }

    public function edit(int $id): View
    {
        $this->authorizeAbility('edit');

        $availability = AvailabilityCalendar::findOrFail($id);

        return view('admin.availability_calendars.edit', [
            'availability' => $availability,
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'roomTypes' => RoomType::orderBy('name')->get(['id', 'name', 'property_id']),
            'formAction' => route('admin.availability_calendars.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $availability = AvailabilityCalendar::findOrFail($id);
        $data = $this->validateAvailability($request);
        $availability->fill($data)->recalcAvailable()->save();

        flash()->success(__('admin.availability.updated'));

        return redirect()->route('admin.availability_calendars.index', [
            'property_id' => $availability->property_id,
            'start' => $availability->available_date->toDateString(),
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        AvailabilityCalendar::findOrFail($id)->delete();

        flash()->success(__('admin.availability.deleted'));

        return redirect()->route('admin.availability_calendars.index');
    }

    /**
     * Inline cell update from the availability grid.
     */
    public function updateCell(Request $request): JsonResponse
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'available_date' => ['required', 'date'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'blocked_rooms' => ['nullable', 'integer', 'min:0'],
            'stop_sell' => ['nullable', 'boolean'],
        ]);

        $roomType = RoomType::findOrFail($data['room_type_id']);

        $dateStr = CarbonImmutable::parse($data['available_date'])->toDateString();
        $row = AvailabilityCalendar::firstOrNew([
            'room_type_id' => $roomType->id,
        ]);
        $existing = AvailabilityCalendar::query()
            ->where('room_type_id', $roomType->id)
            ->whereDate('available_date', $dateStr)
            ->first();
        if ($existing) {
            $row = $existing;
        } else {
            $row->room_type_id = $roomType->id;
            $row->available_date = $dateStr;
        }
        $row->property_id = $roomType->property_id;
        if (isset($data['total_rooms'])) {
            $row->total_rooms = $data['total_rooms'];
        } elseif (! $row->exists) {
            $row->total_rooms = (int) ($roomType->total_rooms ?? 0);
        }
        if (isset($data['blocked_rooms'])) {
            $row->blocked_rooms = $data['blocked_rooms'];
        }
        if (array_key_exists('stop_sell', $data)) {
            $row->stop_sell = (bool) $data['stop_sell'];
        }
        $row->recalcAvailable()->save();

        return response()->json([
            'ok' => true,
            'row' => [
                'id' => $row->id,
                'total_rooms' => $row->total_rooms,
                'booked_rooms' => $row->booked_rooms,
                'blocked_rooms' => $row->blocked_rooms,
                'available_rooms' => $row->available_rooms,
                'stop_sell' => (bool) $row->stop_sell,
            ],
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:0,6'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'blocked_rooms' => ['nullable', 'integer', 'min:0'],
            'stop_sell' => ['nullable', 'boolean'],
        ]);

        $roomType = RoomType::findOrFail($data['room_type_id']);
        $start = CarbonImmutable::parse($data['start_date']);
        $end = CarbonImmutable::parse($data['end_date']);
        $daysOfWeek = collect($data['days_of_week'] ?? [])->map(fn ($d) => (int) $d)->all();

        $hasAny = $request->filled('total_rooms') || $request->filled('blocked_rooms') || $request->has('stop_sell');
        if (! $hasAny) {
            return response()->json(['ok' => false, 'message' => __('admin.availability.bulk_no_fields')], 422);
        }

        $count = DB::transaction(function () use ($roomType, $start, $end, $daysOfWeek, $request, $data) {
            $touched = 0;
            for ($d = $start; $d->lte($end); $d = $d->addDay()) {
                if ($daysOfWeek && ! in_array($d->dayOfWeek, $daysOfWeek, true)) {
                    continue;
                }

                $existing = AvailabilityCalendar::query()
                    ->where('room_type_id', $roomType->id)
                    ->whereDate('available_date', $d->toDateString())
                    ->first();
                if ($existing) {
                    $row = $existing;
                } else {
                    $row = new AvailabilityCalendar([
                        'room_type_id' => $roomType->id,
                        'available_date' => $d->toDateString(),
                    ]);
                }
                $row->property_id = $roomType->property_id;
                if ($request->filled('total_rooms')) {
                    $row->total_rooms = (int) $data['total_rooms'];
                }
                if ($request->filled('blocked_rooms')) {
                    $row->blocked_rooms = (int) $data['blocked_rooms'];
                }
                if ($request->has('stop_sell')) {
                    $row->stop_sell = $request->boolean('stop_sell');
                }
                $row->recalcAvailable()->save();
                $touched++;
            }

            return $touched;
        });

        return response()->json([
            'ok' => true,
            'updated' => $count,
            'message' => __('admin.availability.bulk_applied', ['count' => $count]),
        ]);
    }

    protected function parseStart(?string $raw): CarbonImmutable
    {
        try {
            return $raw ? CarbonImmutable::parse($raw)->startOfDay() : CarbonImmutable::today();
        } catch (\Throwable $e) {
            return CarbonImmutable::today();
        }
    }

    protected function validateAvailability(Request $request): array
    {
        $data = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'available_date' => ['required', 'date'],
            'total_rooms' => ['required', 'integer', 'min:0'],
            'booked_rooms' => ['nullable', 'integer', 'min:0'],
            'blocked_rooms' => ['nullable', 'integer', 'min:0'],
            'stop_sell' => ['nullable', 'boolean'],
        ]);
        $data['booked_rooms'] = $data['booked_rooms'] ?? 0;
        $data['blocked_rooms'] = $data['blocked_rooms'] ?? 0;
        $data['stop_sell'] = $request->boolean('stop_sell');

        return $data;
    }
}
