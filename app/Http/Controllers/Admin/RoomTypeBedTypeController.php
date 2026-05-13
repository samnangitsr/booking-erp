<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomTypeBedType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoomTypeBedTypeController extends BaseCrudController
{
    protected string $model = RoomTypeBedType::class;
    protected string $routeName = 'room_type_bed_type';
    protected string $viewName = 'room_type_bed_type';
    protected string $permissionModule = 'properties';
    protected string $singularLabel = 'properties';
    protected string $headingKey = 'properties';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
