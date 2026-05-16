<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manages the framework-style polymorphic `notifications` table created by
 * the migration template. Unlike the other modules the primary key is a UUID
 * so we override the resource methods to accept a string id.
 */
class NotificationController extends BaseCrudController
{
    protected string $model = Notification::class;
    protected string $routeName = 'notifications';
    protected string $viewName = 'notifications';
    protected string $permissionModule = 'settings';
    protected string $singularLabel = 'notification';
    protected string $headingKey = 'admin.nav.notifications';

    protected array $columns = [
        ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
        ['data' => 'notifiable_type', 'name' => 'notifiable_type', 'title' => 'Notifiable'],
        ['data' => 'notifiable_id', 'name' => 'notifiable_id', 'title' => 'Notifiable ID'],
        ['data' => 'read_at', 'name' => 'read_at', 'title' => 'Read At'],
        ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
    ];

    protected function rules(?Model $row = null): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'notifiable_type' => ['required', 'string', 'max:255'],
            'notifiable_id' => ['required', 'integer'],
            'data' => ['nullable'],
            'read_at' => ['nullable', 'date'],
        ];
    }
}
