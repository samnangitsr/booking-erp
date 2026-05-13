<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PermissionController extends BaseCrudController
{
    protected string $model = Permission::class;
    protected string $routeName = 'permissions';
    protected string $viewName = 'permissions';
    protected string $permissionModule = 'roles';
    protected string $singularLabel = 'permissions';
    protected string $headingKey = 'permissions';

    protected array $columns = [
        ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
        ['data' => 'module', 'name' => 'module', 'title' => 'Module'],
        ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
    ];

    protected function rules(?Model $row = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($row?->id)],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'module' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
