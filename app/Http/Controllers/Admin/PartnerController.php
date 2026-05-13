<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PartnerController extends BaseCrudController
{
    protected string $model = Partner::class;
    protected string $routeName = 'partners';
    protected string $viewName = 'partners';
    protected string $permissionModule = 'partners';
    protected string $singularLabel = 'partners';
    protected string $headingKey = 'partners';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'partner_code',
                'name' => 'partner_code',
                'title' => 'Code',
            ],
            [
                'data' => 'business_name',
                'name' => 'business_name',
                'title' => 'Business',
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
