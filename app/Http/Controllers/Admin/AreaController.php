<?php

namespace App\Http\Controllers\Admin;

use App\Models\Area;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AreaController extends BaseCrudController
{
    protected string $model = Area::class;
    protected string $routeName = 'areas';
    protected string $viewName = 'areas';
    protected string $permissionModule = 'locations';
    protected string $singularLabel = 'areas';
    protected string $headingKey = 'areas';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'slug',
                'name' => 'slug',
                'title' => 'Slug',
            ],
            [
                'data' => 'status',
                'name' => 'status',
                'title' => 'Status',
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
