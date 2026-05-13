<?php

namespace App\Http\Controllers\Admin;

use App\Models\PromotionRoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PromotionRoomTypeController extends BaseCrudController
{
    protected string $model = PromotionRoomType::class;
    protected string $routeName = 'promotion_room_types';
    protected string $viewName = 'promotion_room_types';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
