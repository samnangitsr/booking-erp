<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReportExportController extends BaseCrudController
{
    protected string $model = ReportExport::class;
    protected string $routeName = 'report_exports';
    protected string $viewName = 'report_exports';
    protected string $permissionModule = 'reports';
    protected string $singularLabel = 'reports';
    protected string $headingKey = 'reports';



    protected function rules(?Model $row = null): array
    {
        return [];
    }
}
