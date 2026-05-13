<?php

namespace App\Http\Controllers\Admin;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NotificationTemplateController extends BaseCrudController
{
    protected string $model = NotificationTemplate::class;
    protected string $routeName = 'notification_templates';
    protected string $viewName = 'notification_templates';
    protected string $permissionModule = 'settings';
    protected string $singularLabel = 'settings';
    protected string $headingKey = 'settings';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
