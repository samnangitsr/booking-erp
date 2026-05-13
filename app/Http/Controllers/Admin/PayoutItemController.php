<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayoutItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PayoutItemController extends BaseCrudController
{
    protected string $model = PayoutItem::class;
    protected string $routeName = 'payout_items';
    protected string $viewName = 'payout_items';
    protected string $permissionModule = 'finance';
    protected string $singularLabel = 'finance';
    protected string $headingKey = 'finance';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
