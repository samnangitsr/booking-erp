<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CustomerDocumentController extends BaseCrudController
{
    protected string $model = CustomerDocument::class;
    protected string $routeName = 'customer_documents';
    protected string $viewName = 'customer_documents';
    protected string $permissionModule = 'customers';
    protected string $singularLabel = 'customers';
    protected string $headingKey = 'customers';

    protected array $columns = [
            [
                'data' => 'id',
                'name' => 'id',
                'title' => 'ID',
            ],
            [
                'data' => 'document_type',
                'name' => 'document_type',
                'title' => 'Type',
            ],
            [
                'data' => 'document_no',
                'name' => 'document_no',
                'title' => 'No',
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
