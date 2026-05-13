<?php

namespace App\Http\Controllers\Admin;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GuestController extends BaseCrudController
{
    protected string $model = Guest::class;
    protected string $routeName = 'guests';
    protected string $viewName = 'guests';
    protected string $permissionModule = 'bookings';
    protected string $singularLabel = 'bookings';
    protected string $headingKey = 'bookings';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'first_name',
                'name' => 'first_name',
                'title' => 'First name',
            ],
            [
                'data' => 'last_name',
                'name' => 'last_name',
                'title' => 'Last name',
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
                'data' => 'is_primary',
                'name' => 'is_primary',
                'title' => 'Primary',
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
