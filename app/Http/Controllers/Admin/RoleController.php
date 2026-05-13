<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends BaseCrudController
{
    protected string $model = Role::class;
    protected string $routeName = 'roles';
    protected string $viewName = 'roles';
    protected string $permissionModule = 'roles';
    protected string $singularLabel = 'roles';
    protected string $headingKey = 'roles';

    protected array $columns = [
        ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
        ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
    ];

    protected function rules(?Model $row = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($row?->id)],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validated($request);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        $role = Role::create($data);
        $role->syncPermissions($permissions);

        flash()->success(__('admin.common.created'));

        return redirect()->route('admin.roles.index');
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeAbility('edit');

        $role = Role::findOrFail($id);
        $data = $this->validated($request, $role);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role->fill($data)->save();
        $role->syncPermissions($permissions);

        flash()->success(__('admin.common.updated'));

        return redirect()->route('admin.roles.index');
    }

    protected function formOptions(?Model $row): array
    {
        return [
            'permissions' => Permission::orderBy('module')->orderBy('name')->get(),
            'assignedPermissions' => $row ? $row->permissions->pluck('id')->all() : [],
        ];
    }
}
