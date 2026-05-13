<?php

namespace App\Http\Controllers\Admin;

use App\Models\CancellationPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CancellationPolicyController extends BaseCrudController
{
    protected string $model = CancellationPolicy::class;
    protected string $routeName = 'cancellation_policies';
    protected string $viewName = 'cancellation_policies';
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
                'data' => 'cancel_before_days',
                'name' => 'cancel_before_days',
                'title' => 'Days',
            ],
            [
                'data' => 'refund_percentage',
                'name' => 'refund_percentage',
                'title' => 'Refund %',
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
