<?php

namespace App\Http\Controllers\Admin;

use App\Models\Refund;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RefundController extends BaseCrudController
{
    protected string $model = Refund::class;
    protected string $routeName = 'refunds';
    protected string $viewName = 'refunds';
    protected string $permissionModule = 'finance';
    protected string $singularLabel = 'finance';
    protected string $headingKey = 'finance';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'refund_no',
                'name' => 'refund_no',
                'title' => 'Refund #',
            ],
            [
                'data' => 'amount',
                'name' => 'amount',
                'title' => 'Amount',
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
