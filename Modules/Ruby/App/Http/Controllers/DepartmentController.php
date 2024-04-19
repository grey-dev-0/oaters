<?php

namespace Modules\Ruby\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Ruby\App\Models\Department;

class DepartmentController extends Controller{
    public function postIndex(){
        return tap(\DataTables::of(Department::with(['translations' => fn($locales) => $locales->select('department_id', 'name')->whereLocale(app()->getLocale()), 'head'])->withCount(['subordinates', 'vacancies' => fn($vacancies) => $vacancies->whereActive(true)])->groupBy('r_departments.id'))
            ->editColumn('head', fn($department) => $department->head->first()->name?? '-')->filter(function($query){
                \DataTablesHelper::filterByDate($query, ['created_at']);
            }), function($datatable){
                \DataTablesHelper::formatTimestampColumns($datatable, ['created_at']);
            })->make();
    }
}