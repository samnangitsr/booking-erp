<?php

namespace App\Http\Controllers\Admin;

use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyController extends BaseCrudController
{
    protected string $model = Property::class;
    protected string $routeName = 'properties';
    protected string $viewName = 'properties';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'properties';
    protected string $headingKey = 'properties';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'property_code',
                'name' => 'property_code',
                'title' => 'Code',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'star_rating',
                'name' => 'star_rating',
                'title' => 'Stars',
            ],
            [
                'data' => 'status',
                'name' => 'status',
                'title' => 'Status',
            ],
            [
                'data' => 'approval_status',
                'name' => 'approval_status',
                'title' => 'Approval',
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
