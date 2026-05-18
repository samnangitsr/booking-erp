<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\NearbyPlace;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyPolicy;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PropertyController extends BaseCrudController
{
    protected string $model = Property::class;

    protected string $routeName = 'properties';

    protected string $viewName = 'properties';

    protected string $permissionModule = 'properties';

    protected string $singularLabel = 'properties';

    protected string $headingKey = 'admin.nav.properties';

    protected array $with = ['propertyType', 'country', 'city'];

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        $stats = [
            'total' => Property::count(),
            'active' => Property::where('status', 'active')->count(),
            'pending_approval' => Property::where('approval_status', 'pending')->count(),
            'featured' => Property::where('is_featured', true)->count(),
            'total_room_types' => DB::table('room_types')->whereNull('deleted_at')->count(),
            'total_rooms' => DB::table('rooms')->whereNull('deleted_at')->count(),
        ];

        $properties = Property::query()
            ->with(['propertyType', 'city', 'country', 'images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')])
            ->withCount(['roomTypes', 'rooms', 'bookings'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('approval_status'), fn ($q) => $q->where('approval_status', $request->string('approval_status')))
            ->when($request->filled('property_type_id'), fn ($q) => $q->where('property_type_id', $request->integer('property_type_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->string('q'));
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                        ->orWhere('property_code', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.properties.index', [
            'stats' => $stats,
            'properties' => $properties,
            'propertyTypes' => PropertyType::orderBy('name')->get(),
            'statuses' => Property::STATUSES,
            'approvalStatuses' => Property::APPROVAL_STATUSES,
            'datatableUrl' => route('admin.properties.index', ['datatable' => 1] + $request->query()),
            'columns' => $this->datatableColumns(),
            'createUrl' => route('admin.properties.create'),
            'heading' => $this->headingKey,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = Property::query()
            ->with(['propertyType', 'city', 'country'])
            ->withCount(['roomTypes', 'rooms'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('approval_status'), fn ($q) => $q->where('approval_status', $request->string('approval_status')))
            ->when($request->filled('property_type_id'), fn ($q) => $q->where('property_type_id', $request->integer('property_type_id')));

        return DataTables::of($query)
            ->editColumn('name', function (Property $p) {
                $url = route('admin.properties.show', $p->id);
                $featured = $p->is_featured ? '<span class="badge bg-warning-subtle text-warning ms-1"><i class="bi bi-star-fill"></i></span>' : '';

                return '<a href="'.$url.'" class="fw-semibold text-primary">'.e($p->name).'</a> '.$featured.'<div class="text-muted small">'.e($p->property_code).'</div>';
            })
            ->editColumn('star_rating', fn (Property $p) => str_repeat('<i class="bi bi-star-fill text-warning"></i>', (int) floor((float) $p->star_rating)).str_repeat('<i class="bi bi-star text-warning"></i>', max(0, 5 - (int) floor((float) $p->star_rating))))
            ->editColumn('status', fn (Property $p) => view('admin.properties._partials.status_badge', ['type' => 'status', 'status' => $p->status])->render())
            ->editColumn('approval_status', fn (Property $p) => view('admin.properties._partials.status_badge', ['type' => 'approval', 'status' => $p->approval_status])->render())
            ->addColumn('rooms_summary', fn (Property $p) => '<span class="text-muted">'.$p->room_types_count.' '.__('admin.properties.types').' · '.$p->rooms_count.' '.__('admin.properties.rooms').'</span>')
            ->addColumn('location', function (Property $p) {
                $parts = array_filter([$p->city?->name, $p->country?->name]);

                return '<div>'.e(implode(', ', $parts)).'</div>';
            })
            ->addColumn('action', function (Property $p) {
                return view('admin.properties._partials.row_actions', [
                    'showUrl' => route('admin.properties.show', $p->id),
                    'editUrl' => route('admin.properties.edit', $p->id),
                    'deleteUrl' => route('admin.properties.destroy', $p->id),
                    'canEdit' => $this->userCan('edit'),
                    'canShow' => $this->userCan('view'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['name', 'star_rating', 'status', 'approval_status', 'rooms_summary', 'location', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.properties.create', [
            'property' => new Property([
                'status' => 'inactive',
                'approval_status' => 'pending',
                'star_rating' => 0,
                'is_featured' => false,
            ]),
            'options' => $this->formOptions(null),
            'formAction' => route('admin.properties.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
            'images' => collect(),
            'policies' => collect(),
            'nearbyPlaces' => collect(),
            'selectedAmenityIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateProperty($request);

        $property = DB::transaction(function () use ($data, $request) {
            $p = new Property($data['property']);
            $p->slug = $this->uniqueSlug($p->name);
            $p->property_code = $p->property_code ?: $this->generateCode();
            $p->created_by = $request->user()?->id;
            $p->save();

            $p->amenities()->sync($data['amenity_ids']);
            $this->syncImages($p, $data['images']);
            $this->syncPolicies($p, $data['policies']);
            $this->syncNearbyPlaces($p, $data['nearby_places']);

            return $p;
        });

        flash()->success(__('admin.properties.created'));

        return redirect()->route('admin.properties.show', $property->id);
    }

    public function show(int|string $id): View
    {
        $this->authorizeAbility('view');

        $property = Property::query()
            ->with([
                'propertyType', 'company', 'country', 'city', 'area',
                'amenities', 'policies', 'nearbyPlaces', 'images',
                'roomTypes' => fn ($q) => $q->withCount('rooms')->orderBy('name'),
            ])
            ->withCount(['roomTypes', 'rooms', 'bookings'])
            ->findOrFail($id);

        $roomStatusCounts = DB::table('rooms')
            ->where('property_id', $id)
            ->whereNull('deleted_at')
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        return view('admin.properties.show', [
            'property' => $property,
            'roomStatusCounts' => $roomStatusCounts,
            'roomStatuses' => \App\Models\Room::STATUSES,
        ]);
    }

    public function edit(int|string $id): View
    {
        $this->authorizeAbility('edit');

        $property = Property::with(['images', 'policies', 'nearbyPlaces', 'amenities'])->findOrFail($id);

        return view('admin.properties.edit', [
            'property' => $property,
            'options' => $this->formOptions($property),
            'formAction' => route('admin.properties.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
            'images' => $property->images,
            'policies' => $property->policies,
            'nearbyPlaces' => $property->nearbyPlaces,
            'selectedAmenityIds' => $property->amenities->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $property = Property::findOrFail($id);
        $data = $this->validateProperty($request, $property);

        DB::transaction(function () use ($property, $data, $request) {
            $property->fill($data['property']);
            if ($property->isDirty('name')) {
                $property->slug = $this->uniqueSlug($property->name, $property->id);
            }
            $property->updated_by = $request->user()?->id;
            $property->save();

            $property->amenities()->sync($data['amenity_ids']);
            $this->syncImages($property, $data['images']);
            $this->syncPolicies($property, $data['policies']);
            $this->syncNearbyPlaces($property, $data['nearby_places']);
        });

        flash()->success(__('admin.properties.updated'));

        return redirect()->route('admin.properties.show', $property->id);
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        Property::findOrFail($id)->delete();
        flash()->success(__('admin.properties.deleted'));

        return redirect()->route('admin.properties.index');
    }

    /* -------------------------------------------------------------------- */
    /*                                Helpers                               */
    /* -------------------------------------------------------------------- */

    protected function formOptions(?Model $property): array
    {
        return [
            'property_types' => PropertyType::orderBy('name')->get(['id', 'name']),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'cities' => City::orderBy('name')->get(['id', 'name', 'country_id']),
            'areas' => Area::orderBy('name')->get(['id', 'name', 'city_id']),
            'amenities' => Amenity::query()
                ->where('amenity_type', 'property')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'icon']),
            'statuses' => Property::STATUSES,
            'approval_statuses' => Property::APPROVAL_STATUSES,
            'policy_types' => ['checkin', 'checkout', 'child', 'pet', 'smoking', 'cancellation', 'payment', 'other'],
        ];
    }

    protected function datatableColumns(): array
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => '#'],
            ['data' => 'name', 'name' => 'name', 'title' => __('admin.properties.name')],
            ['data' => 'location', 'name' => 'location', 'title' => __('admin.properties.location'), 'orderable' => false, 'searchable' => false],
            ['data' => 'star_rating', 'name' => 'star_rating', 'title' => __('admin.properties.stars'), 'orderable' => true, 'searchable' => false],
            ['data' => 'rooms_summary', 'name' => 'rooms_summary', 'title' => __('admin.properties.rooms_summary'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('admin.common.status')],
            ['data' => 'approval_status', 'name' => 'approval_status', 'title' => __('admin.properties.approval')],
            ['data' => 'action', 'name' => 'action', 'title' => __('admin.common.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }

    private function validateProperty(Request $request, ?Property $property = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'property_code' => ['nullable', 'string', 'max:50'],
            'property_type_id' => ['nullable', 'integer', 'exists:property_types,id'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'star_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:'.implode(',', Property::STATUSES)],
            'approval_status' => ['required', 'in:'.implode(',', Property::APPROVAL_STATUSES)],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],

            'images' => ['nullable', 'array'],
            'images.*.id' => ['nullable', 'integer'],
            'images.*.image_path' => ['nullable', 'string', 'max:2048'],
            'images.*.title' => ['nullable', 'string', 'max:255'],
            'images.*.is_cover' => ['sometimes', 'boolean'],

            'policies' => ['nullable', 'array'],
            'policies.*.id' => ['nullable', 'integer'],
            'policies.*.policy_type' => ['required_with:policies.*.title', 'in:checkin,checkout,child,pet,smoking,cancellation,payment,other'],
            'policies.*.title' => ['nullable', 'string', 'max:255'],
            'policies.*.description' => ['nullable', 'string', 'max:2000'],

            'nearby_places' => ['nullable', 'array'],
            'nearby_places.*.id' => ['nullable', 'integer'],
            'nearby_places.*.name' => ['nullable', 'string', 'max:255'],
            'nearby_places.*.place_type' => ['nullable', 'string', 'max:100'],
            'nearby_places.*.distance_km' => ['nullable', 'numeric', 'min:0'],
        ];

        $request->validate($rules);

        $propertyFields = $request->only([
            'company_id', 'partner_id', 'property_type_id',
            'country_id', 'city_id', 'area_id', 'property_code',
            'name', 'star_rating', 'description', 'address',
            'latitude', 'longitude', 'phone', 'email',
            'check_in_time', 'check_out_time', 'min_check_in_age',
            'status', 'approval_status',
        ]);
        $propertyFields['is_featured'] = (bool) $request->input('is_featured', false);

        return [
            'property' => $propertyFields,
            'amenity_ids' => array_values($request->input('amenities', [])),
            'images' => array_values($request->input('images', [])),
            'policies' => array_values($request->input('policies', [])),
            'nearby_places' => array_values($request->input('nearby_places', [])),
        ];
    }

    private function syncImages(Property $property, array $rows): void
    {
        $keep = [];
        $hasCover = false;
        foreach ($rows as $row) {
            $path = trim($row['image_path'] ?? '');
            if ($path === '') {
                continue;
            }
            $isCover = (bool) ($row['is_cover'] ?? false);
            $hasCover = $hasCover || $isCover;
            $img = PropertyImage::firstOrNew([
                'id' => $row['id'] ?? 0,
            ]);
            $img->property_id = $property->id;
            $img->image_path = $path;
            $img->title = $row['title'] ?? null;
            $img->is_cover = $isCover;
            $img->sort_order = $row['sort_order'] ?? 0;
            $img->status = 'active';
            $img->save();
            $keep[] = $img->id;
        }
        if (! $hasCover && ! empty($keep)) {
            PropertyImage::whereIn('id', $keep)->orderBy('sort_order')->first()?->update(['is_cover' => true]);
        }
        PropertyImage::where('property_id', $property->id)->whereNotIn('id', $keep ?: [0])->delete();
    }

    private function syncPolicies(Property $property, array $rows): void
    {
        $keep = [];
        foreach ($rows as $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $policy = PropertyPolicy::firstOrNew(['id' => $row['id'] ?? 0]);
            $policy->property_id = $property->id;
            $policy->policy_type = $row['policy_type'] ?? 'other';
            $policy->title = $title;
            $policy->description = $row['description'] ?? null;
            $policy->status = $row['status'] ?? 'active';
            $policy->save();
            $keep[] = $policy->id;
        }
        PropertyPolicy::where('property_id', $property->id)->whereNotIn('id', $keep ?: [0])->delete();
    }

    private function syncNearbyPlaces(Property $property, array $rows): void
    {
        $keep = [];
        foreach ($rows as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $place = NearbyPlace::firstOrNew(['id' => $row['id'] ?? 0]);
            $place->property_id = $property->id;
            $place->name = $name;
            $place->place_type = $row['place_type'] ?? null;
            $place->distance_km = $row['distance_km'] ?? null;
            $place->description = $row['description'] ?? null;
            $place->save();
            $keep[] = $place->id;
        }
        NearbyPlace::where('property_id', $property->id)->whereNotIn('id', $keep ?: [0])->delete();
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name) ?: 'property';
        $slug = $base;
        $i = 2;
        while (Property::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function generateCode(): string
    {
        do {
            $code = 'PRP'.strtoupper(Str::random(6));
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }
}
