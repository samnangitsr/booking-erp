<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingItemDailyRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BookingItemDailyRateController extends BaseCrudController
{
    protected string $model = BookingItemDailyRate::class;
    protected string $routeName = 'booking_item_daily_rates';
    protected string $viewName = 'booking_item_daily_rates';
    protected string $permissionModule = 'bookings';
    protected string $singularLabel = 'bookings';
    protected string $headingKey = 'bookings';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
