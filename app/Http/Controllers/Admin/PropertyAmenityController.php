<?php

namespace App\Http\Controllers\Admin;

use App\Models\PropertyAmenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyAmenityController extends BaseCrudController
{
    protected string $model = PropertyAmenity::class;
    protected string $routeName = 'property_amenity';
    protected string $viewName = 'property_amenity';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'properties';
    protected string $headingKey = 'properties';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
