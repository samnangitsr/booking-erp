<?php

namespace App\Http\Controllers\Admin;

use App\Models\LoginHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoginHistoryController extends BaseCrudController
{
    protected string $model = LoginHistory::class;
    protected string $routeName = 'login_histories';
    protected string $viewName = 'login_histories';
    protected string $permissionModule = 'reports';
    protected string $singularLabel = 'reports';
    protected string $headingKey = 'reports';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
