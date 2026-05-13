<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TransferController extends BaseCrudController
{
    protected string $model = Transfer::class;
    protected string $routeName = 'transfers';
    protected string $viewName = 'transfers';
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
                'data' => 'vehicle_type',
                'name' => 'vehicle_type',
                'title' => 'Vehicle',
            ],
            [
                'data' => 'origin',
                'name' => 'origin',
                'title' => 'Origin',
            ],
            [
                'data' => 'destination',
                'name' => 'destination',
                'title' => 'Destination',
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
