<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckInOutLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CheckInOutLogController extends BaseCrudController
{
    protected string $model = CheckInOutLog::class;
    protected string $routeName = 'check_in_out_logs';
    protected string $viewName = 'check_in_out_logs';
    protected string $permissionModule = 'bookings';
    protected string $singularLabel = 'bookings';
    protected string $headingKey = 'bookings';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
