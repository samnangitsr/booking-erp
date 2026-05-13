<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AmenityController extends BaseCrudController
{
    protected string $model = Amenity::class;
    protected string $routeName = 'amenities';
    protected string $viewName = 'amenities';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'amenities';
    protected string $headingKey = 'amenities';

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
                'data' => 'amenity_type',
                'name' => 'amenity_type',
                'title' => 'Type',
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
