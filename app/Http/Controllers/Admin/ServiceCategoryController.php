<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ServiceCategoryController extends BaseCrudController
{
    protected string $model = ServiceCategory::class;
    protected string $routeName = 'service_categories';
    protected string $viewName = 'service_categories';
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
