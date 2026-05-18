<?php

namespace App\Http\Controllers\Admin;

use App\Models\DailyRate;
use App\Models\Property;
use App\Models\RatePlan;
use App\Models\RoomType;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DailyRateController extends BaseCrudController
{
    protected string $model = DailyRate::class;

    protected string $routeName = 'daily_rates';

    protected string $viewName = 'daily_rates';

    protected string $permissionModule = 'rates';

    protected string $singularLabel = 'rates';

    protected string $headingKey = 'admin.nav.daily_rates';

    /**
     * Default number of days rendered horizontally in the calendar grid.
     */
    protected const GRID_DAYS = 14;

    public function index(Request $request): View
    {
        $this->authorizeAbility('view');

        $properties = Property::orderBy('name')->get(['id', 'name']);
        $propertyId = $request->integer('property_id') ?: ($properties->first()->id ?? null);

        $start = $this->parseStart($request->input('start'));
        $end = $start->addDays(self::GRID_DAYS - 1);

        $ratePlans = RatePlan::query()
            ->with('roomType:id,name')
            ->when($propertyId, fn ($q) => $q->where('property_id', $propertyId))
            ->where('status', 'active')
            ->orderBy('room_type_id')
            ->orderBy('name')
            ->get();

        $rates = DailyRate::query()
            ->whereIn('rate_plan_id', $ratePlans->pluck('id'))
            ->whereBetween('rate_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy(fn (DailyRate $r) => $r->rate_plan_id.'|'.$r->rate_date->toDateString());

        $dates = collect();
        for ($d = $start; $d->lte($end); $d = $d->addDay()) {
            $dates->push($d);
        }

        return view('admin.daily_rates.index', [
            'properties' => $properties,
            'propertyId' => $propertyId,
            'ratePlans' => $ratePlans,
            'rates' => $rates,
            'dates' => $dates,
            'start' => $start,
            'end' => $end,
            'prevStart' => $start->subDays(self::GRID_DAYS),
            'nextStart' => $start->addDays(self::GRID_DAYS),
            'today' => CarbonImmutable::today(),
            'heading' => $this->headingKey,
        ]);
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.daily_rates.create', [
            'dailyRate' => new DailyRate(['currency_code' => 'USD', 'min_stay' => 1]),
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'ratePlans' => RatePlan::with('roomType:id,name')->orderBy('name')->get(),
            'formAction' => route('admin.daily_rates.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateDailyRate($request);
        $this->fillFromRatePlan($data);
        DailyRate::updateOrCreate(
            [
                'room_type_id' => $data['room_type_id'],
                'rate_plan_id' => $data['rate_plan_id'],
                'rate_date' => $data['rate_date'],
            ],
            $data,
        );

        flash()->success(__('admin.daily_rates.created'));

        return redirect()->route('admin.daily_rates.index', [
            'property_id' => $data['property_id'],
            'start' => $data['rate_date'],
        ]);
    }

    public function show(int|string $id): View
    {
        $this->authorizeAbility('view');

        $rate = DailyRate::with(['property', 'roomType', 'ratePlan'])->findOrFail($id);

        return view('admin.daily_rates.show', compact('rate'));
    }

    public function edit(int|string $id): View
    {
        $this->authorizeAbility('edit');

        $dailyRate = DailyRate::findOrFail($id);

        return view('admin.daily_rates.edit', [
            'dailyRate' => $dailyRate,
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'ratePlans' => RatePlan::with('roomType:id,name')->orderBy('name')->get(),
            'formAction' => route('admin.daily_rates.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $dailyRate = DailyRate::findOrFail($id);
        $data = $this->validateDailyRate($request, $dailyRate);
        $this->fillFromRatePlan($data);
        $dailyRate->update($data);

        flash()->success(__('admin.daily_rates.updated'));

        return redirect()->route('admin.daily_rates.index', [
            'property_id' => $data['property_id'],
            'start' => $data['rate_date'],
        ]);
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        DailyRate::findOrFail($id)->delete();

        flash()->success(__('admin.daily_rates.deleted'));

        return redirect()->route('admin.daily_rates.index');
    }

    /**
     * Inline cell update from the calendar grid.
     */
    public function updateCell(Request $request): JsonResponse
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'rate_plan_id' => ['required', 'integer', 'exists:rate_plans,id'],
            'rate_date' => ['required', 'date'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'stop_sell' => ['nullable', 'boolean'],
            'min_stay' => ['nullable', 'integer', 'min:1'],
        ]);

        $ratePlan = RatePlan::findOrFail($data['rate_plan_id']);

        $dateStr = CarbonImmutable::parse($data['rate_date'])->toDateString();
        $row = DailyRate::query()
            ->where('rate_plan_id', $ratePlan->id)
            ->where('room_type_id', $ratePlan->room_type_id)
            ->whereDate('rate_date', $dateStr)
            ->first();
        if (! $row) {
            $row = new DailyRate([
                'rate_plan_id' => $ratePlan->id,
                'room_type_id' => $ratePlan->room_type_id,
                'rate_date' => $dateStr,
            ]);
        }
        $row->property_id = $ratePlan->property_id;
        $row->currency_code = $row->currency_code ?: 'USD';
        if (array_key_exists('base_price', $data) && $data['base_price'] !== null) {
            $row->base_price = $data['base_price'];
            $row->adult_price = $data['base_price'];
        }
        if (array_key_exists('min_stay', $data) && $data['min_stay'] !== null) {
            $row->min_stay = $data['min_stay'];
        }
        if (array_key_exists('stop_sell', $data)) {
            $row->stop_sell = (bool) $data['stop_sell'];
        }
        $row->save();

        return response()->json([
            'ok' => true,
            'row' => [
                'id' => $row->id,
                'base_price' => $row->base_price,
                'stop_sell' => (bool) $row->stop_sell,
                'min_stay' => $row->min_stay,
            ],
        ]);
    }

    /**
     * Bulk apply pricing/restrictions across a date range, room-type, and
     * rate-plan combination. All inserts/updates run inside a transaction.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'rate_plan_id' => ['required', 'integer', 'exists:rate_plans,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:0,6'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'adult_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'extra_bed_price' => ['nullable', 'numeric', 'min:0'],
            'min_stay' => ['nullable', 'integer', 'min:1'],
            'max_stay' => ['nullable', 'integer', 'min:1'],
            'stop_sell' => ['nullable', 'boolean'],
            'closed_to_arrival' => ['nullable', 'boolean'],
            'closed_to_departure' => ['nullable', 'boolean'],
        ]);

        $ratePlan = RatePlan::findOrFail($data['rate_plan_id']);
        $start = CarbonImmutable::parse($data['start_date']);
        $end = CarbonImmutable::parse($data['end_date']);
        $daysOfWeek = collect($data['days_of_week'] ?? [])->map(fn ($d) => (int) $d)->all();

        $updates = collect([
            'base_price', 'adult_price', 'child_price', 'extra_bed_price',
            'min_stay', 'max_stay',
        ])
            ->filter(fn ($k) => $request->filled($k))
            ->mapWithKeys(fn ($k) => [$k => $data[$k]])
            ->all();

        foreach (['stop_sell', 'closed_to_arrival', 'closed_to_departure'] as $flag) {
            if ($request->has($flag)) {
                $updates[$flag] = $request->boolean($flag);
            }
        }

        if (empty($updates)) {
            return response()->json(['ok' => false, 'message' => __('admin.daily_rates.bulk_no_fields')], 422);
        }

        $count = DB::transaction(function () use ($ratePlan, $start, $end, $daysOfWeek, $updates) {
            $touched = 0;
            for ($d = $start; $d->lte($end); $d = $d->addDay()) {
                if ($daysOfWeek && ! in_array($d->dayOfWeek, $daysOfWeek, true)) {
                    continue;
                }

                $dateStr = $d->toDateString();
                $row = DailyRate::query()
                    ->where('rate_plan_id', $ratePlan->id)
                    ->where('room_type_id', $ratePlan->room_type_id)
                    ->whereDate('rate_date', $dateStr)
                    ->first();
                if (! $row) {
                    $row = new DailyRate([
                        'rate_plan_id' => $ratePlan->id,
                        'room_type_id' => $ratePlan->room_type_id,
                        'rate_date' => $dateStr,
                    ]);
                    $row->property_id = $ratePlan->property_id;
                    $row->currency_code = 'USD';
                }
                $row->property_id = $ratePlan->property_id;
                if (! $row->currency_code) {
                    $row->currency_code = 'USD';
                }
                foreach ($updates as $field => $value) {
                    $row->{$field} = $value;
                }
                $row->save();

                $touched++;
            }

            return $touched;
        });

        return response()->json([
            'ok' => true,
            'updated' => $count,
            'message' => __('admin.daily_rates.bulk_applied', ['count' => $count]),
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

    protected function fillFromRatePlan(array &$data): void
    {
        if (! empty($data['rate_plan_id'])) {
            $ratePlan = RatePlan::find($data['rate_plan_id']);
            if ($ratePlan) {
                $data['property_id'] = $ratePlan->property_id;
                $data['room_type_id'] = $ratePlan->room_type_id;
            }
        }
    }

    protected function validateDailyRate(Request $request, ?DailyRate $dailyRate = null): array
    {
        return $request->validate([
            'rate_plan_id' => ['required', 'integer', 'exists:rate_plans,id'],
            'rate_date' => ['required', 'date'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'adult_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'extra_bed_price' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['required', 'string', 'size:3'],
            'min_stay' => ['nullable', 'integer', 'min:1'],
            'max_stay' => ['nullable', 'integer', 'min:1'],
            'stop_sell' => ['nullable', 'boolean'],
            'closed_to_arrival' => ['nullable', 'boolean'],
            'closed_to_departure' => ['nullable', 'boolean'],
        ]);
    }
}
