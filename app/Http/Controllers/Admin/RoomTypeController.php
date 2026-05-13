<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use App\Models\BedType;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class RoomTypeController extends BaseCrudController
{
    protected string $model = RoomType::class;

    protected string $routeName = 'room_types';

    protected string $viewName = 'room_types';

    protected string $permissionModule = 'properties';

    protected string $singularLabel = 'room_types';

    protected string $headingKey = 'admin.nav.room_types';

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        $stats = [
            'total' => RoomType::count(),
            'active' => RoomType::where('status', 'active')->count(),
            'properties_with_room_types' => RoomType::select('property_id')->distinct()->count('property_id'),
            'physical_rooms' => DB::table('rooms')->whereNull('deleted_at')->count(),
        ];

        return view('admin.room_types.index', [
            'stats' => $stats,
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'statuses' => RoomType::STATUSES,
            'datatableUrl' => route('admin.room_types.index', ['datatable' => 1] + $request->query()),
            'columns' => $this->datatableColumns(),
            'createUrl' => route('admin.room_types.create'),
            'heading' => $this->headingKey,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = RoomType::query()
            ->with(['property'])
            ->withCount('rooms')
            ->when($request->filled('property_id'), fn ($q) => $q->where('property_id', $request->integer('property_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));

        return DataTables::of($query)
            ->editColumn('name', function (RoomType $rt) {
                return '<a href="'.route('admin.room_types.show', $rt->id).'" class="fw-semibold text-primary">'.e($rt->name).'</a>'.
                    '<div class="text-muted small">'.e($rt->room_type_code).'</div>';
            })
            ->addColumn('property_name', fn (RoomType $rt) => $rt->property?->name ?? '—')
            ->addColumn('capacity', fn (RoomType $rt) => '<i class="bi bi-person-fill"></i> '.$rt->max_adults.' / <i class="bi bi-person"></i> '.$rt->max_children.' <span class="text-muted small">('.$rt->max_occupancy.' '.__('admin.room_types.max_occupancy').')</span>')
            ->editColumn('base_price', fn (RoomType $rt) => '<span class="fw-semibold">'.number_format((float) $rt->base_price, 2).'</span>')
            ->addColumn('rooms_count_disp', fn (RoomType $rt) => '<span class="badge bg-secondary-subtle text-secondary">'.$rt->rooms_count.' '.__('admin.properties.rooms').'</span>')
            ->editColumn('status', fn (RoomType $rt) => view('admin.properties._partials.status_badge', ['type' => 'status', 'status' => $rt->status])->render())
            ->addColumn('action', function (RoomType $rt) {
                return view('admin.properties._partials.row_actions', [
                    'showUrl' => route('admin.room_types.show', $rt->id),
                    'editUrl' => route('admin.room_types.edit', $rt->id),
                    'deleteUrl' => route('admin.room_types.destroy', $rt->id),
                    'canEdit' => $this->userCan('edit'),
                    'canShow' => $this->userCan('view'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['name', 'capacity', 'base_price', 'rooms_count_disp', 'status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.room_types.create', [
            'roomType' => new RoomType([
                'status' => 'active',
                'max_adults' => 2,
                'max_children' => 0,
                'max_occupancy' => 2,
                'room_size_unit' => 'sqm',
                'base_price' => 0,
                'total_rooms' => 0,
            ]),
            'options' => $this->formOptions(null),
            'formAction' => route('admin.room_types.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
            'images' => collect(),
            'selectedAmenityIds' => [],
            'bedTypeQuantities' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateRoomType($request);

        $roomType = DB::transaction(function () use ($data) {
            $rt = new RoomType($data['room_type']);
            $rt->slug = $this->uniqueSlug($rt->property_id, $rt->name);
            $rt->room_type_code = $rt->room_type_code ?: $this->generateCode($rt->property_id);
            $rt->save();

            $rt->amenities()->sync($data['amenity_ids']);
            $rt->bedTypes()->sync($data['bed_types']);
            $this->syncImages($rt, $data['images']);

            return $rt;
        });

        flash()->success(__('admin.room_types.created'));

        return redirect()->route('admin.room_types.show', $roomType->id);
    }

    public function show(int $id): View
    {
        $this->authorizeAbility('view');

        $roomType = RoomType::query()
            ->with([
                'property', 'amenities', 'images', 'bedTypes',
                'rooms' => fn ($q) => $q->orderBy('floor')->orderBy('room_number'),
            ])
            ->findOrFail($id);

        $roomStatusCounts = $roomType->rooms->groupBy('status')->map->count();

        return view('admin.room_types.show', [
            'roomType' => $roomType,
            'roomStatusCounts' => $roomStatusCounts,
            'roomStatuses' => \App\Models\Room::STATUSES,
        ]);
    }

    public function edit(int $id): View
    {
        $this->authorizeAbility('edit');

        $roomType = RoomType::with(['amenities', 'images', 'bedTypes'])->findOrFail($id);

        return view('admin.room_types.edit', [
            'roomType' => $roomType,
            'options' => $this->formOptions($roomType),
            'formAction' => route('admin.room_types.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
            'images' => $roomType->images,
            'selectedAmenityIds' => $roomType->amenities->pluck('id')->all(),
            'bedTypeQuantities' => $roomType->bedTypes->mapWithKeys(fn ($bt) => [$bt->id => $bt->pivot->quantity])->all(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $roomType = RoomType::findOrFail($id);
        $data = $this->validateRoomType($request, $roomType);

        DB::transaction(function () use ($roomType, $data) {
            $roomType->fill($data['room_type']);
            if ($roomType->isDirty('name')) {
                $roomType->slug = $this->uniqueSlug($roomType->property_id, $roomType->name, $roomType->id);
            }
            $roomType->save();

            $roomType->amenities()->sync($data['amenity_ids']);
            $roomType->bedTypes()->sync($data['bed_types']);
            $this->syncImages($roomType, $data['images']);
        });

        flash()->success(__('admin.room_types.updated'));

        return redirect()->route('admin.room_types.show', $roomType->id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        RoomType::findOrFail($id)->delete();
        flash()->success(__('admin.room_types.deleted'));

        return redirect()->route('admin.room_types.index');
    }

    /* --------------------------- helpers --------------------------- */

    protected function formOptions(?Model $row): array
    {
        return [
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'amenities' => Amenity::query()
                ->where('amenity_type', 'room')
                ->orWhere('amenity_type', 'property')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'icon']),
            'bed_types' => BedType::where('status', 'active')->orderBy('name')->get(['id', 'name', 'capacity']),
            'statuses' => RoomType::STATUSES,
            'size_units' => ['sqm', 'sqft'],
        ];
    }

    protected function datatableColumns(): array
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => '#'],
            ['data' => 'name', 'name' => 'name', 'title' => __('admin.room_types.name')],
            ['data' => 'property_name', 'name' => 'property.name', 'title' => __('admin.nav.properties'), 'orderable' => false],
            ['data' => 'capacity', 'name' => 'max_adults', 'title' => __('admin.room_types.capacity'), 'orderable' => false, 'searchable' => false],
            ['data' => 'base_price', 'name' => 'base_price', 'title' => __('admin.room_types.base_price')],
            ['data' => 'rooms_count_disp', 'name' => 'rooms_count', 'title' => __('admin.properties.rooms'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('admin.common.status')],
            ['data' => 'action', 'name' => 'action', 'title' => __('admin.common.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }

    private function validateRoomType(Request $request, ?RoomType $roomType = null): array
    {
        $rules = [
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'room_type_code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'max_adults' => ['required', 'integer', 'min:1'],
            'max_children' => ['nullable', 'integer', 'min:0'],
            'max_occupancy' => ['required', 'integer', 'min:1'],
            'room_size' => ['nullable', 'numeric', 'min:0'],
            'room_size_unit' => ['nullable', 'in:sqm,sqft'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', RoomType::STATUSES)],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],
            'bed_types' => ['nullable', 'array'],
            'bed_types.*' => ['integer', 'min:0'],

            'images' => ['nullable', 'array'],
            'images.*.id' => ['nullable', 'integer'],
            'images.*.image_path' => ['nullable', 'string', 'max:2048'],
            'images.*.title' => ['nullable', 'string', 'max:255'],
            'images.*.is_cover' => ['sometimes', 'boolean'],
        ];

        $request->validate($rules);

        $bedTypes = [];
        foreach ((array) $request->input('bed_types', []) as $btId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $bedTypes[(int) $btId] = ['quantity' => $qty];
            }
        }

        return [
            'room_type' => $request->only([
                'property_id', 'room_type_code', 'name', 'description',
                'max_adults', 'max_children', 'max_occupancy',
                'room_size', 'room_size_unit', 'total_rooms',
                'base_price', 'status',
            ]),
            'amenity_ids' => array_values($request->input('amenities', [])),
            'bed_types' => $bedTypes,
            'images' => array_values($request->input('images', [])),
        ];
    }

    private function syncImages(RoomType $roomType, array $rows): void
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
            $img = RoomTypeImage::firstOrNew(['id' => $row['id'] ?? 0]);
            $img->room_type_id = $roomType->id;
            $img->image_path = $path;
            $img->title = $row['title'] ?? null;
            $img->is_cover = $isCover;
            $img->sort_order = $row['sort_order'] ?? 0;
            $img->save();
            $keep[] = $img->id;
        }
        if (! $hasCover && ! empty($keep)) {
            RoomTypeImage::whereIn('id', $keep)->orderBy('sort_order')->first()?->update(['is_cover' => true]);
        }
        RoomTypeImage::where('room_type_id', $roomType->id)->whereNotIn('id', $keep ?: [0])->delete();
    }

    private function uniqueSlug(int $propertyId, string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name) ?: 'room-type';
        $slug = $base;
        $i = 2;
        while (RoomType::where('property_id', $propertyId)
            ->where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function generateCode(int $propertyId): string
    {
        do {
            $code = 'RT'.strtoupper(Str::random(5));
        } while (RoomType::where('property_id', $propertyId)->where('room_type_code', $code)->exists());

        return $code;
    }
}
