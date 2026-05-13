<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BranchController extends BaseCrudController
{
    protected string $model = Branch::class;
    protected string $routeName = 'branches';
    protected string $viewName = 'branches';
    protected string $permissionModule = 'organization';
    protected string $singularLabel = 'branches';
    protected string $headingKey = 'branches';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'branch_code',
                'name' => 'branch_code',
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
