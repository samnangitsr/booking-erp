<?php

namespace App\Http\Controllers\Admin;

use App\Models\DailyRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DailyRateController extends BaseCrudController
{
    protected string $model = DailyRate::class;
    protected string $routeName = 'daily_rates';
    protected string $viewName = 'daily_rates';
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
                'data' => 'date',
                'name' => 'date',
                'title' => 'Date',
            ],
            [
                'data' => 'price_single',
                'name' => 'price_single',
                'title' => 'Single',
            ],
            [
                'data' => 'price_double',
                'name' => 'price_double',
                'title' => 'Double',
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
