<?php

namespace App\Http\Controllers\Admin;

use App\Models\NearbyPlace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NearbyPlaceController extends BaseCrudController
{
    protected string $model = NearbyPlace::class;
    protected string $routeName = 'nearby_places';
    protected string $viewName = 'nearby_places';
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
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'category',
                'name' => 'category',
                'title' => 'Category',
            ],
            [
                'data' => 'distance_value',
                'name' => 'distance_value',
                'title' => 'Distance',
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
