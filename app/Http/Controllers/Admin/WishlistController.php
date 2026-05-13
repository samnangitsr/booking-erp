<?php

namespace App\Http\Controllers\Admin;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class WishlistController extends BaseCrudController
{
    protected string $model = Wishlist::class;
    protected string $routeName = 'wishlists';
    protected string $viewName = 'wishlists';
    protected string $permissionModule = 'marketing';
    protected string $singularLabel = 'marketing';
    protected string $headingKey = 'marketing';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
