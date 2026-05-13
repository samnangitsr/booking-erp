<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ServiceBookingController extends BaseCrudController
{
    protected string $model = ServiceBooking::class;
    protected string $routeName = 'service_bookings';
    protected string $viewName = 'service_bookings';
    protected string $permissionModule = 'services';
    protected string $singularLabel = 'services';
    protected string $headingKey = 'services';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
