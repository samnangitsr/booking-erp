<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CustomerController extends BaseCrudController
{
    protected string $model = Customer::class;
    protected string $routeName = 'customers';
    protected string $viewName = 'customers';
    protected string $permissionModule = 'customers';
    protected string $singularLabel = 'customers';
    protected string $headingKey = 'customers';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'customer_code',
                'name' => 'customer_code',
                'title' => 'Code',
            ],
            [
                'data' => 'first_name',
                'name' => 'first_name',
                'title' => 'First name',
            ],
            [
                'data' => 'last_name',
                'name' => 'last_name',
                'title' => 'Last name',
            ],
            [
                'data' => 'phone',
                'name' => 'phone',
                'title' => 'Phone',
            ],
            [
                'data' => 'email',
                'name' => 'email',
                'title' => 'Email',
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
