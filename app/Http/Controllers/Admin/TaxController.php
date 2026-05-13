<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TaxController extends BaseCrudController
{
    protected string $model = Tax::class;
    protected string $routeName = 'taxes';
    protected string $viewName = 'taxes';
    protected string $permissionModule = 'rates';
    protected string $singularLabel = 'rates';
    protected string $headingKey = 'rates';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'rate_type',
                'name' => 'rate_type',
                'title' => 'Type',
            ],
            [
                'data' => 'rate_value',
                'name' => 'rate_value',
                'title' => 'Value',
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
