<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BookingController extends BaseCrudController
{
    protected string $model = Booking::class;
    protected string $routeName = 'bookings';
    protected string $viewName = 'bookings';
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
                'data' => 'booking_no',
                'name' => 'booking_no',
                'title' => 'Booking #',
            ],
            [
                'data' => 'check_in_date',
                'name' => 'check_in_date',
                'title' => 'Check-in',
            ],
            [
                'data' => 'check_out_date',
                'name' => 'check_out_date',
                'title' => 'Check-out',
            ],
            [
                'data' => 'grand_total',
                'name' => 'grand_total',
                'title' => 'Total',
            ],
            [
                'data' => 'booking_status',
                'name' => 'booking_status',
                'title' => 'Status',
            ],
            [
                'data' => 'payment_status',
                'name' => 'payment_status',
                'title' => 'Payment',
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
