<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyImageController extends BaseCrudController
{
    protected string $model = PropertyImage::class;
    protected string $routeName = 'property_images';
    protected string $viewName = 'property_images';
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
                'data' => 'sort_order',
                'name' => 'sort_order',
                'title' => 'Order',
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
