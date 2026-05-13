<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChildAgePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChildAgePolicyController extends BaseCrudController
{
    protected string $model = ChildAgePolicy::class;
    protected string $routeName = 'child_age_policies';
    protected string $viewName = 'child_age_policies';
    protected string $permissionModule = 'rates';
    protected string $singularLabel = 'rates';
    protected string $headingKey = 'rates';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
