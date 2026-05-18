<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseCrudController
{
    protected string $model = User::class;
    protected string $routeName = 'users';
    protected string $viewName = 'users';
    protected string $permissionModule = 'users';
    protected string $singularLabel = 'users';
    protected string $headingKey = 'users';

    protected array $columns = [
        ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
        ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
        ['data' => 'user_type', 'name' => 'user_type', 'title' => 'Type'],
        ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
    ];

    protected array $with = ['company', 'branch'];

    protected function rules(?Model $row = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($row?->id)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => $row ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
            'user_type' => ['required', 'in:super_admin,admin,staff,partner,customer'],
            'status' => ['required', 'in:active,inactive,blocked'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeAbility('create');

        $data = $this->validated($request);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->syncRoles($roles);

        flash()->success(__('admin.common.created'));

        return redirect()->route('admin.users.index');
    }

    public function update(Request $request, int|string $id): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeAbility('edit');

        $user = User::findOrFail($id);
        $data = $this->validated($request, $user);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->fill($data)->save();
        $user->syncRoles($roles);

        flash()->success(__('admin.common.updated'));

        return redirect()->route('admin.users.index');
    }

    protected function formOptions(?Model $row): array
    {
        return [
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'assignedRoles' => $row ? $row->roles->pluck('id')->all() : [],
        ];
    }
}
