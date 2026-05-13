<?php

namespace App\Http\Controllers\Admin;

use App\Models\AvailabilityCalendar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AvailabilityCalendarController extends BaseCrudController
{
    protected string $model = AvailabilityCalendar::class;
    protected string $routeName = 'availability_calendars';
    protected string $viewName = 'availability_calendars';
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
                'data' => 'available_rooms',
                'name' => 'available_rooms',
                'title' => 'Available',
            ],
            [
                'data' => 'total_rooms',
                'name' => 'total_rooms',
                'title' => 'Total',
            ],
            [
                'data' => 'is_open',
                'name' => 'is_open',
                'title' => 'Open',
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
