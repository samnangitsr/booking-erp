<?php

namespace App\Http\Controllers\Admin;

use App\Models\CancellationPolicy;
use App\Models\DailyRate;
use App\Models\Property;
use App\Models\RatePlan;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class RatePlanController extends BaseCrudController
{
    protected string $model = RatePlan::class;

    protected string $routeName = 'rate_plans';

    protected string $viewName = 'rate_plans';

    protected string $permissionModule = 'rates';

    protected string $singularLabel = 'rates';

    protected string $headingKey = 'admin.nav.rate_plans';

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        $stats = [
            'total' => RatePlan::count(),
            'active' => RatePlan::where('status', 'active')->count(),
            'properties' => RatePlan::distinct('property_id')->count('property_id'),
            'refundable' => RatePlan::where('is_refundable', true)->count(),
        ];

        return view('admin.rate_plans.index', [
            'stats' => $stats,
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'roomTypes' => RoomType::orderBy('name')->get(['id', 'name', 'property_id']),
            'mealPlans' => RatePlan::MEAL_PLANS,
            'paymentPolicies' => RatePlan::PAYMENT_POLICIES,
            'statuses' => RatePlan::STATUSES,
            'datatableUrl' => route('admin.rate_plans.index', ['datatable' => 1] + $request->query()),
            'columns' => $this->datatableColumns(),
            'createUrl' => route('admin.rate_plans.create'),
            'heading' => $this->headingKey,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = RatePlan::query()
            ->with(['property:id,name', 'roomType:id,name'])
            ->withCount('dailyRates')
            ->when($request->filled('property_id'), fn ($q) => $q->where('property_id', $request->integer('property_id')))
            ->when($request->filled('room_type_id'), fn ($q) => $q->where('room_type_id', $request->integer('room_type_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('meal_plan'), fn ($q) => $q->where('meal_plan', $request->string('meal_plan')));

        return DataTables::of($query)
            ->editColumn('name', function (RatePlan $rp) {
                $url = route('admin.rate_plans.show', $rp->id);

                return '<a href="'.$url.'" class="fw-semibold text-primary">'.e($rp->name).'</a>'
                    .'<div class="text-muted small">'.e($rp->rate_plan_code).'</div>';
            })
            ->addColumn('property', fn (RatePlan $rp) => e($rp->property?->name).'<div class="text-muted small">'.e($rp->roomType?->name).'</div>')
            ->editColumn('meal_plan', fn (RatePlan $rp) => view('admin.rate_plans._partials.meal_plan_badge', ['value' => $rp->meal_plan])->render())
            ->editColumn('payment_policy', fn (RatePlan $rp) => '<span class="badge bg-light text-dark">'.e(__('admin.rate_plans.payment_policy_value.'.$rp->payment_policy)).'</span>')
            ->editColumn('is_refundable', fn (RatePlan $rp) => $rp->is_refundable
                ? '<span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-arrow-counterclockwise me-1"></i>'.__('admin.rate_plans.refundable').'</span>'
                : '<span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-x-circle me-1"></i>'.__('admin.rate_plans.non_refundable').'</span>')
            ->editColumn('status', fn (RatePlan $rp) => view('admin.rate_plans._partials.status_badge', ['status' => $rp->status])->render())
            ->addColumn('rates_count', fn (RatePlan $rp) => '<span class="badge bg-secondary-subtle text-secondary">'.$rp->daily_rates_count.'</span>')
            ->addColumn('action', function (RatePlan $rp) {
                return view('admin.properties._partials.row_actions', [
                    'showUrl' => route('admin.rate_plans.show', $rp->id),
                    'editUrl' => route('admin.rate_plans.edit', $rp->id),
                    'deleteUrl' => route('admin.rate_plans.destroy', $rp->id),
                    'canEdit' => $this->userCan('edit'),
                    'canShow' => $this->userCan('view'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['name', 'property', 'meal_plan', 'payment_policy', 'is_refundable', 'status', 'rates_count', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.rate_plans.create', [
            'ratePlan' => new RatePlan([
                'meal_plan' => 'none',
                'payment_policy' => 'pay_at_property',
                'is_refundable' => true,
                'status' => 'active',
            ]),
            'options' => $this->formOptions(),
            'formAction' => route('admin.rate_plans.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateRatePlan($request);
        $ratePlan = RatePlan::create($data);

        flash()->success(__('admin.rate_plans.created'));

        return redirect()->route('admin.rate_plans.show', $ratePlan->id);
    }

    public function show(int $id): View
    {
        $this->authorizeAbility('view');

        $ratePlan = RatePlan::with(['property', 'roomType', 'cancellationPolicy'])
            ->withCount('dailyRates')
            ->findOrFail($id);

        $today = now()->startOfDay();
        $end = $today->copy()->addDays(13);

        $previewRates = DailyRate::query()
            ->where('rate_plan_id', $id)
            ->whereBetween('rate_date', [$today->toDateString(), $end->toDateString()])
            ->orderBy('rate_date')
            ->get(['rate_date', 'base_price', 'stop_sell']);

        $previewDates = collect();
        for ($d = $today->copy(); $d->lte($end); $d->addDay()) {
            $row = $previewRates->firstWhere('rate_date', $d->copy()->startOfDay());
            $previewDates->push([
                'date' => $d->copy(),
                'base_price' => $row?->base_price,
                'stop_sell' => (bool) ($row?->stop_sell ?? false),
            ]);
        }

        return view('admin.rate_plans.show', [
            'ratePlan' => $ratePlan,
            'previewDates' => $previewDates,
        ]);
    }

    public function edit(int $id): View
    {
        $this->authorizeAbility('edit');

        $ratePlan = RatePlan::findOrFail($id);

        return view('admin.rate_plans.edit', [
            'ratePlan' => $ratePlan,
            'options' => $this->formOptions($ratePlan),
            'formAction' => route('admin.rate_plans.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $ratePlan = RatePlan::findOrFail($id);
        $data = $this->validateRatePlan($request, $ratePlan);
        $ratePlan->update($data);

        flash()->success(__('admin.rate_plans.updated'));

        return redirect()->route('admin.rate_plans.show', $ratePlan->id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        RatePlan::findOrFail($id)->delete();

        flash()->success(__('admin.rate_plans.deleted'));

        return redirect()->route('admin.rate_plans.index');
    }

    /**
     * AJAX cascading endpoint reused by Daily Rates / Availability calendars
     * to populate the room-type dropdown when the property changes.
     */
    public function roomTypesForProperty(Request $request): JsonResponse
    {
        $propertyId = $request->integer('property_id');

        $roomTypes = RoomType::query()
            ->when($propertyId, fn ($q) => $q->where('property_id', $propertyId))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($rt) => ['id' => $rt->id, 'text' => $rt->name]);

        return response()->json(['data' => $roomTypes]);
    }

    protected function validateRatePlan(Request $request, ?RatePlan $ratePlan = null): array
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'rate_plan_code' => [
                'nullable', 'string', 'max:50',
                Rule::unique('rate_plans', 'rate_plan_code')
                    ->where(fn ($q) => $q->where('property_id', $request->integer('property_id')))
                    ->ignore($ratePlan?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'meal_plan' => ['required', Rule::in(RatePlan::MEAL_PLANS)],
            'payment_policy' => ['required', Rule::in(RatePlan::PAYMENT_POLICIES)],
            'cancellation_policy_id' => ['nullable', 'integer', 'exists:cancellation_policies,id'],
            'is_refundable' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(RatePlan::STATUSES)],
        ]);

        $validated['rate_plan_code'] = $validated['rate_plan_code'] ?? null;
        if (! $validated['rate_plan_code']) {
            $validated['rate_plan_code'] = 'RP'.strtoupper(substr(uniqid(), -6));
        }
        $validated['is_refundable'] = $request->boolean('is_refundable');

        return $validated;
    }

    protected function formOptions(?\Illuminate\Database\Eloquent\Model $row = null): array
    {
        return [
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'room_types' => RoomType::with('property:id,name')->orderBy('name')->get(['id', 'name', 'property_id']),
            'meal_plans' => RatePlan::MEAL_PLANS,
            'payment_policies' => RatePlan::PAYMENT_POLICIES,
            'cancellation_policies' => CancellationPolicy::orderBy('name')->get(['id', 'name']),
            'statuses' => RatePlan::STATUSES,
        ];
    }

    protected function datatableColumns(): array
    {
        return [
            ['data' => 'name', 'name' => 'name', 'title' => __('admin.rate_plans.name')],
            ['data' => 'property', 'name' => 'property', 'title' => __('admin.nav.properties'), 'orderable' => false],
            ['data' => 'meal_plan', 'name' => 'meal_plan', 'title' => __('admin.rate_plans.meal_plan')],
            ['data' => 'payment_policy', 'name' => 'payment_policy', 'title' => __('admin.rate_plans.payment_policy')],
            ['data' => 'is_refundable', 'name' => 'is_refundable', 'title' => __('admin.rate_plans.refundable')],
            ['data' => 'rates_count', 'name' => 'rates_count', 'title' => __('admin.rate_plans.rates_count'), 'orderable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('admin.common.status')],
            ['data' => 'action', 'name' => 'action', 'title' => __('admin.common.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }
}
