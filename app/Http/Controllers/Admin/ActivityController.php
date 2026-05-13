<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityController extends BaseCrudController
{
    protected string $model = Activity::class;
    protected string $routeName = 'activities';
    protected string $viewName = 'activities';
    protected string $permissionModule = 'services';
    protected string $singularLabel = 'services';
    protected string $headingKey = 'services';

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
                'data' => 'duration_minutes',
                'name' => 'duration_minutes',
                'title' => 'Duration',
            ],
            [
                'data' => 'base_price',
                'name' => 'base_price',
                'title' => 'Price',
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
