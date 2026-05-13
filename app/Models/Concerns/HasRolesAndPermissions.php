<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

/**
 * Manual role/permission support for any model. Stores assignments via the
 * polymorphic `model_has_roles` / `model_has_permissions` pivots provided
 * by the migration. No third-party package required.
 */
trait HasRolesAndPermissions
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function directPermissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions', 'model_id', 'permission_id');
    }

    public function assignRole(Role|string|int ...$roles): self
    {
        $ids = $this->resolveRoleIds(Arr::flatten($roles));
        $this->roles()->syncWithoutDetaching($ids);
        $this->clearPermissionCache();

        return $this;
    }

    public function syncRoles(array $roles): self
    {
        $ids = $this->resolveRoleIds($roles);
        $this->roles()->sync($ids);
        $this->clearPermissionCache();

        return $this;
    }

    public function removeRole(Role|string|int $role): self
    {
        $ids = $this->resolveRoleIds([$role]);
        $this->roles()->detach($ids);
        $this->clearPermissionCache();

        return $this;
    }

    public function hasRole(string|Role $role): bool
    {
        $name = $role instanceof Role ? $role->name : $role;

        return $this->loadedRoles()->contains(fn (Role $r) => $r->name === $name);
    }

    public function hasPermission(string|Permission $permission): bool
    {
        if (method_exists($this, 'isSuperAdmin') && $this->isSuperAdmin()) {
            return true;
        }

        $name = $permission instanceof Permission ? $permission->name : $permission;

        return $this->allPermissionNames()->contains($name);
    }

    public function givePermissionTo(Permission|string|int ...$permissions): self
    {
        $ids = $this->resolvePermissionIds(Arr::flatten($permissions));
        $this->directPermissions()->syncWithoutDetaching($ids);
        $this->clearPermissionCache();

        return $this;
    }

    public function syncPermissions(array $permissions): self
    {
        $ids = $this->resolvePermissionIds($permissions);
        $this->directPermissions()->sync($ids);
        $this->clearPermissionCache();

        return $this;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    public function allPermissionNames(): \Illuminate\Support\Collection
    {
        $cacheKey = 'permission_names_'.$this->getKey();
        if (! property_exists($this, '_permissionCache')) {
            $this->_permissionCache = [];
        }
        if (isset($this->_permissionCache[$cacheKey])) {
            return $this->_permissionCache[$cacheKey];
        }

        /** @var Collection<int, Role> $roles */
        $roles = $this->loadedRoles();
        $rolePermissions = $roles->flatMap(fn (Role $r) => $r->permissions->pluck('name'));
        $direct = $this->directPermissions->pluck('name');

        $names = $rolePermissions->merge($direct)->unique()->values();

        return $this->_permissionCache[$cacheKey] = $names;
    }

    private function loadedRoles(): Collection
    {
        if (! $this->relationLoaded('roles')) {
            $this->load(['roles.permissions']);
        }

        return $this->roles;
    }

    private function clearPermissionCache(): void
    {
        if (property_exists($this, '_permissionCache')) {
            $this->_permissionCache = [];
        }
        $this->unsetRelation('roles');
        $this->unsetRelation('directPermissions');
    }

    private function resolveRoleIds(array $roles): array
    {
        return collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            if (is_numeric($role)) {
                return (int) $role;
            }

            return Role::query()->where('name', (string) $role)->value('id');
        })->filter()->unique()->values()->all();
    }

    private function resolvePermissionIds(array $permissions): array
    {
        return collect($permissions)->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            if (is_numeric($permission)) {
                return (int) $permission;
            }

            return Permission::query()->where('name', (string) $permission)->value('id');
        })->filter()->unique()->values()->all();
    }
}
