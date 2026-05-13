<?php

namespace App\Http\Controllers\Admin;

use App\Models\Commission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CommissionController extends BaseCrudController
{
    protected string $model = Commission::class;
    protected string $routeName = 'commissions';
    protected string $viewName = 'commissions';
    protected string $permissionModule = 'finance';
    protected string $singularLabel = 'finance';
    protected string $headingKey = 'finance';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
