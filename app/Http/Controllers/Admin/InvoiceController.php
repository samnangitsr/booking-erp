<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InvoiceController extends BaseCrudController
{
    protected string $model = Invoice::class;
    protected string $routeName = 'invoices';
    protected string $viewName = 'invoices';
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
                'data' => 'invoice_no',
                'name' => 'invoice_no',
                'title' => 'Invoice #',
            ],
            [
                'data' => 'grand_total',
                'name' => 'grand_total',
                'title' => 'Total',
            ],
            [
                'data' => 'due_amount',
                'name' => 'due_amount',
                'title' => 'Due',
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
