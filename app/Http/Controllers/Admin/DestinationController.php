<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DestinationController extends BaseCrudController
{
    protected string $model = Destination::class;
    protected string $routeName = 'destinations';
    protected string $viewName = 'destinations';
    protected string $permissionModule = 'locations';
    protected string $singularLabel = 'destinations';
    protected string $headingKey = 'destinations';

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
