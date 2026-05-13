<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PaymentController extends BaseCrudController
{
    protected string $model = Payment::class;
    protected string $routeName = 'payments';
    protected string $viewName = 'payments';
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
                'data' => 'transaction_no',
                'name' => 'transaction_no',
                'title' => 'Tx #',
            ],
            [
                'data' => 'amount',
                'name' => 'amount',
                'title' => 'Amount',
            ],
            [
                'data' => 'direction',
                'name' => 'direction',
                'title' => 'Direction',
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
