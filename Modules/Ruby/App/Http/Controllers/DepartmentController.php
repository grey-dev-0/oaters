<?php

namespace Modules\Ruby\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Ruby\App\Http\Requests\DepartmentRequest;
use Modules\Ruby\App\Models\Department;

class DepartmentController extends Controller{
    public function postIndex(){
        return tap(\DataTables::of(Department::with(['translations' => fn($locales) => $locales->select('department_id', 'name', 'locale')->whereLocale(app()->getLocale()), 'head'])->withCount(['subordinates', 'vacancies' => fn($vacancies) => $vacancies->whereActive(true)])->groupBy('r_departments.id'))
            ->editColumn('head', fn($department) => $department->head->first()->name?? '-')->filter(function($query){
                \DataTablesHelper::filterByDate($query, ['created_at']);
            }), function($datatable){
                \DataTablesHelper::formatTimestampColumns($datatable, ['created_at']);
            })->make();
    }

    public function postCreateOrUpdate(DepartmentRequest $request){
        return $request->handle();
    }

    public function getDepartment(Department $department){
        return response()->json($department->load(['head', 'employees', 'translations'])->setHidden(['translations'])
            ->setAttribute('name_ar', $department->translations->where('locale', 'ar')->first()->name));
    }
}