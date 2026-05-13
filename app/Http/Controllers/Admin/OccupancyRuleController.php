<?php

namespace App\Http\Controllers\Admin;

use App\Models\OccupancyRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OccupancyRuleController extends BaseCrudController
{
    protected string $model = OccupancyRule::class;
    protected string $routeName = 'occupancy_rules';
    protected string $viewName = 'occupancy_rules';
    protected string $permissionModule = 'rates';
    protected string $singularLabel = 'rates';
    protected string $headingKey = 'rates';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
