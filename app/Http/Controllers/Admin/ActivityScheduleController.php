<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivitySchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityScheduleController extends BaseCrudController
{
    protected string $model = ActivitySchedule::class;
    protected string $routeName = 'activity_schedules';
    protected string $viewName = 'activity_schedules';
    protected string $permissionModule = 'services';
    protected string $singularLabel = 'services';
    protected string $headingKey = 'services';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
