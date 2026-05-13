<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomBlock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoomBlockController extends BaseCrudController
{
    protected string $model = RoomBlock::class;
    protected string $routeName = 'room_blocks';
    protected string $viewName = 'room_blocks';
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
                'data' => 'block_reason',
                'name' => 'block_reason',
                'title' => 'Reason',
            ],
            [
                'data' => 'start_date',
                'name' => 'start_date',
                'title' => 'Start',
            ],
            [
                'data' => 'end_date',
                'name' => 'end_date',
                'title' => 'End',
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
