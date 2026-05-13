<?php

namespace App\Http\Controllers\Admin;

use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CouponUsageController extends BaseCrudController
{
    protected string $model = CouponUsage::class;
    protected string $routeName = 'coupon_usages';
    protected string $viewName = 'coupon_usages';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
