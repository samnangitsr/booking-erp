<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingStatusHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BookingStatusHistoryController extends BaseCrudController
{
    protected string $model = BookingStatusHistory::class;
    protected string $routeName = 'booking_status_histories';
    protected string $viewName = 'booking_status_histories';
    protected string $permissionModule = 'bookings';
    protected string $singularLabel = 'bookings';
    protected string $headingKey = 'bookings';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
