<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['company_id', 'name', 'guard_name', 'description'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function givePermissionTo(Permission|string|int ...$permissions): self
    {
        $ids = collect($permissions)->flatten()->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            if (is_numeric($permission)) {
                return (int) $permission;
            }

            return Permission::query()->where('name', (string) $permission)->value('id');
        })->filter()->unique()->all();

        $this->permissions()->syncWithoutDetaching($ids);

        return $this;
    }

    public function syncPermissions(array $permissions): self
    {
        $ids = collect($permissions)->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            if (is_numeric($permission)) {
                return (int) $permission;
            }

            return Permission::query()->where('name', (string) $permission)->value('id');
        })->filter()->unique()->all();

        $this->permissions()->sync($ids);

        return $this;
    }
}
