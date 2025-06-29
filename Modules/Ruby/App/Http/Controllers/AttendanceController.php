<?php
namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ruby\App\Models\DepartmentLocale;
use Modules\Ruby\App\Models\Punch;
use Modules\Sapphire\App\Models\RoleLocale;

class AttendanceController extends Controller{
    public function getIndex(){
        $roles = RoleLocale::whereLocale($locale = app()->getLocale())->whereHas('role', fn($role) => $role->whereIn('name', ['general-manager', 'financial-manager', 'hr-manager', 'director', 'team-lead', 'accountant', 'hr-assistant', 'employee']))->pluck('title', 'role_id');
        $departments = DepartmentLocale::whereLocale($locale)->pluck('name', 'department_id');
        return view('ruby::attendance', compact('roles', 'departments'));
    }

    public function postIndex(){
        return tap(\DataTables::of(Punch::with([
            'contact' => fn($q) => $q->withRoles()->withDepartments()
        ])->select('r_punches.*')), fn($dataTable) => \DataTablesHelper::formatTimestampColumns($dataTable, ['created_at']))
            ->filter(function($query){
                \DataTablesHelper::filterByDate($query, ['r_punches.created_at'], false);
                $columns = \DataTablesHelper::getColumns();
                if(!empty($departments = request("columns.{$columns['contact.departments.name']}.search.value")))
                    $query->whereHas('contact.departments', fn($depts) => $depts->whereIn('r_departments.id', $departments));
                if(!empty($roles = request("columns.{$columns['contact.roles.title']}.search.value")))
                    $query->whereHas('contact.roles', fn($r) => $r->whereIn('id', $roles));
            })->make();
    }
}