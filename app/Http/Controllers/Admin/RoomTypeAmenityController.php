<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomTypeAmenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoomTypeAmenityController extends BaseCrudController
{
    protected string $model = RoomTypeAmenity::class;
    protected string $routeName = 'room_type_amenity';
    protected string $viewName = 'room_type_amenity';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'properties';
    protected string $headingKey = 'properties';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
