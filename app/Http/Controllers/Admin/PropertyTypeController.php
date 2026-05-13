<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyTypeController extends BaseCrudController
{
    protected string $model = PropertyType::class;
    protected string $routeName = 'property_types';
    protected string $viewName = 'property_types';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'property_types';
    protected string $headingKey = 'property_types';

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
