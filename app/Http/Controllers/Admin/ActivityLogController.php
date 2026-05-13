<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogController extends BaseCrudController
{
    protected string $model = ActivityLog::class;
    protected string $routeName = 'activity_logs';
    protected string $viewName = 'activity_logs';
    protected string $permissionModule = 'reports';
    protected string $singularLabel = 'reports';
    protected string $headingKey = 'reports';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
