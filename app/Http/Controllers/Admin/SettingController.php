<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SettingController extends BaseCrudController
{
    protected string $model = Setting::class;
    protected string $routeName = 'settings';
    protected string $viewName = 'settings';
    protected string $permissionModule = 'settings';
    protected string $singularLabel = 'settings';
    protected string $headingKey = 'settings';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'key',
                'name' => 'key',
                'title' => 'Key',
            ],
            [
                'data' => 'group',
                'name' => 'group',
                'title' => 'Group',
            ],
            [
                'data' => 'type',
                'name' => 'type',
                'title' => 'Type',
            ],
            [
                'data' => 'is_public',
                'name' => 'is_public',
                'title' => 'Public',
            ],
            [
                'data' => 'action',
                'name' => 'action',
                'title' => 'Actions',
                'orderable' => false,
                'searchable' => false,
            ],
        ];

    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
