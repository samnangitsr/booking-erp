<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CompanyController extends BaseCrudController
{
    protected string $model = Company::class;
    protected string $routeName = 'companies';
    protected string $viewName = 'companies';
    protected string $permissionModule = 'organization';
    protected string $singularLabel = 'companies';
    protected string $headingKey = 'companies';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'company_code',
                'name' => 'company_code',
                'title' => 'Code',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
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
