<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReviewReply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReviewReplyController extends BaseCrudController
{
    protected string $model = ReviewReply::class;
    protected string $routeName = 'review_replies';
    protected string $viewName = 'review_replies';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
