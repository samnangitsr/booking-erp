<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

/**
 * Abstract base CRUD controller used by every admin module. Subclasses
 * configure module-specific behaviour by overriding the protected properties
 * / methods. This keeps the 70+ generated controllers extremely thin while
 * giving each one a real Yajra DataTables index + Bootstrap 5 form pages.
 */
abstract class BaseCrudController extends Controller
{
    /** Fully-qualified model class for the resource. */
    protected string $model;

    /** Route name segment, e.g. "branches". */
    protected string $routeName;

    /** View namespace (directory under resources/views/admin/). Defaults to routeName. */
    protected string $viewName = '';

    /** Permission module key used for permission gating, e.g. "branches". */
    protected string $permissionModule = '';

    /** Human-readable singular label key for messages. */
    protected string $singularLabel = '';

    /** Default page heading translation key. */
    protected string $headingKey = '';

    /** DataTables column definitions returned to the front-end. */
    protected array $columns = [
        ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
    ];

    /** Eager-load relationships for index/show. */
    protected array $with = [];

    public function index(Request $request): View|JsonResponse
    {
        $this->authorizeAbility('view');

        if ($request->ajax() && $request->boolean('datatable')) {
            return $this->datatable($request);
        }

        return view($this->resolveView('index'), [
            'datatableUrl' => $request->fullUrlWithQuery(['datatable' => 1]),
            'columns' => $this->columns,
            'createUrl' => $this->routeFor('create'),
            'heading' => $this->headingKey ?: $this->routeName,
        ]);
    }

    protected function datatable(Request $request): JsonResponse
    {
        $query = $this->resolveQuery();

        return DataTables::of($query)
            ->addColumn('action', function (Model $row) {
                return view('admin.partials._row_actions', [
                    'editUrl' => $this->routeForId('edit', $row->getKey()),
                    'showUrl' => $this->routeForId('show', $row->getKey()),
                    'deleteUrl' => $this->routeForId('destroy', $row->getKey()),
                    'canEdit' => $this->userCan('edit'),
                    'canShow' => $this->userCan('view'),
                    'canDelete' => $this->userCan('delete'),
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create(): View
    {
        $this->authorizeAbility('create');

        return view($this->resolveView('create'), [
            'item' => new $this->model(),
            'formAction' => $this->routeFor('store'),
            'formMethod' => 'POST',
            'options' => $this->formOptions(null),
            'heading' => $this->headingKey ?: $this->routeName,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validated($request);
        $row = new $this->model($data);
        $row->save();
        $this->afterSave($row, $request, 'created');

        flash()->success(__('admin.common.created'));

        return redirect()->route($this->routePrefix().'.index');
    }

    public function show(int $id): View
    {
        $this->authorizeAbility('view');

        $row = $this->resolveQuery()->findOrFail($id);

        return view($this->resolveView('show'), [
            'item' => $row,
            'heading' => $this->headingKey ?: $this->routeName,
        ]);
    }

    public function edit(int $id): View
    {
        $this->authorizeAbility('edit');

        $row = $this->resolveQuery()->findOrFail($id);

        return view($this->resolveView('edit'), [
            'item' => $row,
            'formAction' => $this->routeForId('update', $id),
            'formMethod' => 'PUT',
            'options' => $this->formOptions($row),
            'heading' => $this->headingKey ?: $this->routeName,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAbility('edit');

        $row = $this->resolveQuery()->findOrFail($id);
        $data = $this->validated($request, $row);
        $row->fill($data);
        $row->save();
        $this->afterSave($row, $request, 'updated');

        flash()->success(__('admin.common.updated'));

        return redirect()->route($this->routePrefix().'.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAbility('delete');

        $row = $this->resolveQuery()->findOrFail($id);
        $row->delete();
        flash()->success(__('admin.common.deleted'));

        return redirect()->route($this->routePrefix().'.index');
    }

    /* ----------------------------- helpers ----------------------------- */

    protected function resolveQuery()
    {
        /** @var class-string<Model> $model */
        $model = $this->model;
        $query = $model::query();
        if (! empty($this->with)) {
            $query->with($this->with);
        }

        return $query;
    }

    protected function validated(Request $request, ?Model $row = null): array
    {
        $rules = $this->rules($row);
        if (empty($rules)) {
            /** @var class-string<Model> $model */
            $model = $this->model;
            $fillable = (new $model)->getFillable();
            return $request->only($fillable);
        }

        return $request->validate($rules);
    }

    /** @return array<string, mixed> */
    protected function rules(?Model $row = null): array
    {
        return [];
    }

    /** Hook called after store/update for related-records syncing. */
    protected function afterSave(Model $row, Request $request, string $action): void
    {
        //
    }

    /** Provides dropdown / select option arrays to the create/edit views. */
    protected function formOptions(?Model $row): array
    {
        return [];
    }

    /* --------------------------- permission glue ------------------------- */

    protected function authorizeAbility(string $action): void
    {
        $module = $this->permissionModule ?: $this->routeName;
        $permission = $module.'.'.$action;
        $user = request()->user();
        if (! $user || ! $user->hasPermission($permission)) {
            abort(403, "Missing permission [{$permission}].");
        }
    }

    protected function userCan(string $action): bool
    {
        $module = $this->permissionModule ?: $this->routeName;
        $user = request()->user();

        return $user ? $user->hasPermission($module.'.'.$action) : false;
    }

    /* --------------------------- url helpers ---------------------------- */

    protected function routePrefix(): string
    {
        return 'admin.'.$this->routeName;
    }

    protected function routeFor(string $action): string
    {
        return route($this->routePrefix().'.'.$action);
    }

    protected function routeForId(string $action, int|string $id): string
    {
        return route($this->routePrefix().'.'.$action, $id);
    }

    protected function resolveView(string $action): string
    {
        $namespace = $this->viewName ?: $this->routeName;
        $candidate = "admin.{$namespace}.{$action}";
        if (view()->exists($candidate)) {
            return $candidate;
        }

        return "admin.partials.generic_{$action}";
    }
}
