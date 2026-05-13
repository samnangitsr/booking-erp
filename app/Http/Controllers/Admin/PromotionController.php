<?php

namespace App\Http\Controllers\Admin;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PromotionController extends BaseCrudController
{
    protected string $model = Promotion::class;
    protected string $routeName = 'promotions';
    protected string $viewName = 'promotions';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'code',
                'name' => 'code',
                'title' => 'Code',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'start_date',
                'name' => 'start_date',
                'title' => 'Start',
            ],
            [
                'data' => 'end_date',
                'name' => 'end_date',
                'title' => 'End',
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
