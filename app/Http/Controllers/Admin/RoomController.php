<?php

namespace App\Http\Controllers\Admin;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class RoomController extends BaseCrudController
{
    protected string $model = Room::class;

    protected string $routeName = 'rooms';

    protected string $viewName = 'rooms';

    protected string $permissionModule = 'properties';

    protected string $singularLabel = 'rooms';

    protected string $headingKey = 'admin.nav.rooms';

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        $propertyId = $request->integer('property_id');
        $statusFilter = $request->string('status')->toString();
        $roomTypeId = $request->integer('room_type_id');

        $properties = Property::orderBy('name')->get(['id', 'name']);

        $defaultPropertyId = $propertyId ?: ($properties->first()->id ?? null);

        $rooms = Room::query()
            ->with(['roomType', 'property'])
            ->when($defaultPropertyId, fn ($q) => $q->where('property_id', $defaultPropertyId))
            ->when($statusFilter !== '', fn ($q) => $q->where('status', $statusFilter))
            ->when($roomTypeId, fn ($q) => $q->where('room_type_id', $roomTypeId))
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        $floors = $rooms->groupBy(fn ($r) => $r->floor ?: '—');

        $statusCounts = $rooms->groupBy('status')->map->count();
        $totalRooms = $rooms->count();

        return view('admin.rooms.index', [
            'properties' => $properties,
            'selectedPropertyId' => $defaultPropertyId,
            'selectedStatus' => $statusFilter,
            'selectedRoomTypeId' => $roomTypeId,
            'roomTypes' => RoomType::where('property_id', $defaultPropertyId)->orderBy('name')->get(['id', 'name']),
            'floors' => $floors,
            'statusCounts' => $statusCounts,
            'totalRooms' => $totalRooms,
            'statuses' => Room::STATUSES,
            'datatableUrl' => route('admin.rooms.index', ['datatable' => 1] + $request->query()),
            'columns' => $this->datatableColumns(),
            'createUrl' => route('admin.rooms.create'),
            'heading' => $this->headingKey,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = Room::query()
            ->with(['property', 'roomType'])
            ->when($request->filled('property_id'), fn ($q) => $q->where('property_id', $request->integer('property_id')))
            ->when($request->filled('room_type_id'), fn ($q) => $q->where('room_type_id', $request->integer('room_type_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));

        return DataTables::of($query)
            ->addColumn('property_name', fn (Room $r) => $r->property?->name ?? '—')
            ->addColumn('room_type_name', fn (Room $r) => $r->roomType?->name ?? '—')
            ->editColumn('status', fn (Room $r) => view('admin.properties._partials.status_badge', ['type' => 'room', 'status' => $r->status])->render())
            ->addColumn('action', function (Room $r) {
                return view('admin.properties._partials.row_actions', [
                    'showUrl' => route('admin.rooms.show', $r->id),
                    'editUrl' => route('admin.rooms.edit', $r->id),
                    'deleteUrl' => route('admin.rooms.destroy', $r->id),
                    'canEdit' => $this->userCan('edit'),
                    'canShow' => $this->userCan('view'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view('admin.rooms.create', [
            'room' => new Room(['status' => 'available']),
            'options' => $this->formOptions(null),
            'formAction' => route('admin.rooms.store'),
            'formMethod' => 'POST',
            'heading' => $this->headingKey,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validateRoom($request);
        $room = Room::create($data);

        flash()->success(__('admin.rooms.created'));

        return redirect()->route('admin.rooms.show', $room->id);
    }

    public function show(int|string $id): View
    {
        $this->authorizeAbility('view');

        $room = Room::with(['property', 'roomType'])->findOrFail($id);

        $upcomingBookings = DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->join('customers', 'customers.id', '=', 'bookings.customer_id')
            ->where('booking_items.room_id', $room->id)
            ->where('bookings.check_out_date', '>=', now()->toDateString())
            ->orderBy('bookings.check_in_date')
            ->select([
                'bookings.id as booking_id',
                'bookings.booking_no',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'bookings.booking_status',
                'customers.first_name',
                'customers.last_name',
            ])
            ->limit(8)
            ->get();

        return view('admin.rooms.show', [
            'room' => $room,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }

    public function edit(int|string $id): View
    {
        $this->authorizeAbility('edit');

        $room = Room::findOrFail($id);

        return view('admin.rooms.edit', [
            'room' => $room,
            'options' => $this->formOptions($room),
            'formAction' => route('admin.rooms.update', $id),
            'formMethod' => 'PUT',
            'heading' => $this->headingKey,
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $room = Room::findOrFail($id);
        $data = $this->validateRoom($request, $room);
        $room->update($data);

        flash()->success(__('admin.rooms.updated'));

        return redirect()->route('admin.rooms.show', $room->id);
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        Room::findOrFail($id)->delete();
        flash()->success(__('admin.rooms.deleted'));

        return redirect()->route('admin.rooms.index');
    }

    /**
     * Quick endpoint to flip a room status from the floor-grid index page.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Room::STATUSES)],
        ]);

        $room = Room::findOrFail($id);
        $room->status = $data['status'];
        $room->save();

        return response()->json([
            'id' => $room->id,
            'status' => $room->status,
            'badge' => view('admin.properties._partials.status_badge', ['type' => 'room', 'status' => $room->status])->render(),
        ]);
    }

    public function roomTypesForProperty(Request $request): JsonResponse
    {
        $propertyId = $request->integer('property_id');
        $roomTypes = RoomType::where('property_id', $propertyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($rt) => ['id' => $rt->id, 'text' => $rt->name]);

        return response()->json(['data' => $roomTypes]);
    }

    /* --------------------------- helpers --------------------------- */

    protected function formOptions(?Model $row): array
    {
        return [
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'room_types' => RoomType::with('property')->orderBy('name')->get(['id', 'name', 'property_id']),
            'statuses' => Room::STATUSES,
        ];
    }

    protected function datatableColumns(): array
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => '#'],
            ['data' => 'room_number', 'name' => 'room_number', 'title' => __('admin.rooms.room_number')],
            ['data' => 'floor', 'name' => 'floor', 'title' => __('admin.rooms.floor')],
            ['data' => 'property_name', 'name' => 'property.name', 'title' => __('admin.nav.properties'), 'orderable' => false],
            ['data' => 'room_type_name', 'name' => 'roomType.name', 'title' => __('admin.nav.room_types'), 'orderable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('admin.common.status')],
            ['data' => 'action', 'name' => 'action', 'title' => __('admin.common.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }

    private function validateRoom(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:'.implode(',', Room::STATUSES)],
        ]);
    }
}
