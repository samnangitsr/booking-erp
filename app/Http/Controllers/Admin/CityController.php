<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CityController extends BaseCrudController
{
    protected string $model = City::class;
    protected string $routeName = 'cities';
    protected string $viewName = 'cities';
    protected string $permissionModule = 'locations';
    protected string $singularLabel = 'cities';
    protected string $headingKey = 'cities';

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
