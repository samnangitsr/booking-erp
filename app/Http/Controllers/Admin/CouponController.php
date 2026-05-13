<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CouponController extends BaseCrudController
{
    protected string $model = Coupon::class;
    protected string $routeName = 'coupons';
    protected string $viewName = 'coupons';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'code',
                'name' => 'code',
                'title' => 'Code',
            ],
            [
                'data' => 'discount_type',
                'name' => 'discount_type',
                'title' => 'Type',
            ],
            [
                'data' => 'discount_value',
                'name' => 'discount_value',
                'title' => 'Value',
            ],
            [
                'data' => 'status',
                'name' => 'status',
                'title' => 'Status',
            ],
            [
                'data' => 'action',
                'name' => 'action',
                'title' => 'Actions',
                'orderable' => false,
                'searchable' => false,
            ],
        ];

    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
