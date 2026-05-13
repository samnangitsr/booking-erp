<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyPolicyController extends BaseCrudController
{
    protected string $model = PropertyPolicy::class;
    protected string $routeName = 'property_policies';
    protected string $viewName = 'property_policies';
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
                'data' => 'title',
                'name' => 'title',
                'title' => 'Title',
            ],
            [
                'data' => 'category',
                'name' => 'category',
                'title' => 'Category',
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
