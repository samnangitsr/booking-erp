<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoomTypeController extends BaseCrudController
{
    protected string $model = RoomType::class;
    protected string $routeName = 'room_types';
    protected string $viewName = 'room_types';
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
                'data' => 'room_type_code',
                'name' => 'room_type_code',
                'title' => 'Code',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'base_price',
                'name' => 'base_price',
                'title' => 'Base price',
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
