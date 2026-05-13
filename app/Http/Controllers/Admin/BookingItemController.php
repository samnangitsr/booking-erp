<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BookingItemController extends BaseCrudController
{
    protected string $model = BookingItem::class;
    protected string $routeName = 'booking_items';
    protected string $viewName = 'booking_items';
    protected string $permissionModule = 'bookings';
    protected string $singularLabel = 'bookings';
    protected string $headingKey = 'bookings';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
