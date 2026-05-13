<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InvoiceItemController extends BaseCrudController
{
    protected string $model = InvoiceItem::class;
    protected string $routeName = 'invoice_items';
    protected string $viewName = 'invoice_items';
    protected string $permissionModule = 'finance';
    protected string $singularLabel = 'finance';
    protected string $headingKey = 'finance';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
