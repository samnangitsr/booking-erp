<?php

namespace App\Http\Controllers\Admin;

use App\Models\RatePlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RatePlanController extends BaseCrudController
{
    protected string $model = RatePlan::class;
    protected string $routeName = 'rate_plans';
    protected string $viewName = 'rate_plans';
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
                'data' => 'plan_code',
                'name' => 'plan_code',
                'title' => 'Code',
            ],
            [
                'data' => 'name',
                'name' => 'name',
                'title' => 'Name',
            ],
            [
                'data' => 'meal_plan',
                'name' => 'meal_plan',
                'title' => 'Meal',
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
