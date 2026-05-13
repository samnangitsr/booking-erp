<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CountryController extends BaseCrudController
{
    protected string $model = Country::class;
    protected string $routeName = 'countries';
    protected string $viewName = 'countries';
    protected string $permissionModule = 'locations';
    protected string $singularLabel = 'countries';
    protected string $headingKey = 'countries';

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
                'data' => 'iso_code',
                'name' => 'iso_code',
                'title' => 'ISO',
            ],
            [
                'data' => 'phone_code',
                'name' => 'phone_code',
                'title' => 'Phone code',
            ],
            [
                'data' => 'currency_code',
                'name' => 'currency_code',
                'title' => 'Currency',
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
