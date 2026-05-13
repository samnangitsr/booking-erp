<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReviewController extends BaseCrudController
{
    protected string $model = Review::class;
    protected string $routeName = 'reviews';
    protected string $viewName = 'reviews';
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
                'data' => 'rating',
                'name' => 'rating',
                'title' => 'Rating',
            ],
            [
                'data' => 'title',
                'name' => 'title',
                'title' => 'Title',
            ],
            [
                'data' => 'is_approved',
                'name' => 'is_approved',
                'title' => 'Approved',
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
