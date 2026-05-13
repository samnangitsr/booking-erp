<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyContact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyContactController extends BaseCrudController
{
    protected string $model = PropertyContact::class;
    protected string $routeName = 'property_contacts';
    protected string $viewName = 'property_contacts';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'properties';
    protected string $headingKey = 'properties';

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
                'data' => 'position',
                'name' => 'position',
                'title' => 'Position',
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
