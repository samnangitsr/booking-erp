<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyFee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyFeeController extends BaseCrudController
{
    protected string $model = PropertyFee::class;
    protected string $routeName = 'property_fees';
    protected string $viewName = 'property_fees';
    protected string $permissionModule = 'rates';
    protected string $singularLabel = 'rates';
    protected string $headingKey = 'rates';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
