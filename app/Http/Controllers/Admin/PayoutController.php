<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PayoutController extends BaseCrudController
{
    protected string $model = Payout::class;
    protected string $routeName = 'payouts';
    protected string $viewName = 'payouts';
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
                'data' => 'payout_no',
                'name' => 'payout_no',
                'title' => 'Payout #',
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
