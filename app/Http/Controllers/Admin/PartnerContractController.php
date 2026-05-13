<?php

namespace App\Http\Controllers\Admin;

use App\Models\PartnerContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PartnerContractController extends BaseCrudController
{
    protected string $model = PartnerContract::class;
    protected string $routeName = 'partner_contracts';
    protected string $viewName = 'partner_contracts';
    protected string $permissionModule = 'partners';
    protected string $singularLabel = 'partner_contracts';
    protected string $headingKey = 'partner_contracts';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'contract_no',
                'name' => 'contract_no',
                'title' => 'Contract No',
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
