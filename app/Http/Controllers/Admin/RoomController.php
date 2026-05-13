<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoomController extends BaseCrudController
{
    protected string $model = Room::class;
    protected string $routeName = 'rooms';
    protected string $viewName = 'rooms';
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
                'data' => 'room_no',
                'name' => 'room_no',
                'title' => 'Room #',
            ],
            [
                'data' => 'floor',
                'name' => 'floor',
                'title' => 'Floor',
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
