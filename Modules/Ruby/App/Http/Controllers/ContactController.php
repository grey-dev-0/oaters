<?php

namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\DepartmentLocale;
use Modules\Sapphire\App\Models\RoleLocale;

class ContactController extends Controller{
    public function postSearch(){
        $rolesGroup = ['general-manager', 'financial-manager', 'hr-manager', 'accountant', 'hr-assistant', 'employee',
            'director', 'team-lead'];
        $suggestions= Contact::where(fn($q) => $q->where('name', 'like', '%'.($query = request('query')).'%')->orWhereHas('emails', fn($emails) => $emails->where('address', 'like', "%$query%")))->whereHas('roles', fn($roles) => $roles->whereIn('name', $rolesGroup))->with(['emails' => fn($emails) => $emails->whereDefault(true)])->limit(request('limit'))->get(['id', 'name'])->map(fn($contact) => $contact->only('id') + ['name' => "{$contact->name} - {$contact->emails->first()?->address}"])->pluck('name', 'id');
        return response()->json(compact('suggestions'));
    }

    public function getIndex(){
        $roles = RoleLocale::whereLocale($locale = app()->getLocale())->whereHas('role', fn($role) => $role->whereIn('name', ['general-manager', 'financial-manager', 'hr-manager', 'director', 'team-lead', 'accountant', 'hr-assistant', 'employee']))->pluck('title', 'role_id');
        $departments = DepartmentLocale::whereLocale($locale)->pluck('name', 'department_id');
        return view('ruby::contacts', compact('roles', 'departments'));
    }

    public function postIndex(){
        return tap(\DataTables::of(Contact::activeRecruit()->withRoles()->withDefaultInfo()->withDepartments()->with('applicant:id,recruited_at')->select(['lc_contacts.id', 'name', 'job'])), fn($dataTable) => \DataTablesHelper::formatTimestampColumns($dataTable, ['applicant.recruited_at']))->filter(function($query){
            \DataTablesHelper::filterByDate($query, ['applicant.recruited_at']);
            $columns = \DataTablesHelper::getColumns();
            if(!empty($departments = isset($columns['departments'])? request("columns.{$columns['departments']}.search.value") : [request('department')]))
                $query->where(fn($q) => $q->whereHas('departments', fn($depts) => $depts->whereIn('r_departments.id', $departments))
                    ->orWhereHas('managed_departments', fn($depts) => $depts->whereIn('r_departments.id', $departments)));
            if(!empty($roles = request("columns.{$columns['roles']}.search.value")))
                $query->whereHas('roles', fn($r) => $r->whereIn('id', $roles));
        })->orderColumn('roles', function($query, $direction){
            $query->leftJoin('s_model_has_roles AS mr', fn($join) => $join->on('lc_contacts.id', 'mr.model_id')
                ->where('model_type', Contact::class))->leftJoin('s_role_locales AS rl', 'mr.role_id', 'rl.role_id')->orderBy('rl.title', $direction);
        })->make();
    }

    public function getContact($contact){
        $contact = Contact::withRoles()->withAllInfo()->withDepartments()->withRecruitmentDetails()
            ->with(['leaves' => fn($leaves) => $leaves->select(['contact_id', 'type', 'starts_at', 'ends_at'])
                ->whereStatus(1)->take(5)->orderBy('id', 'desc')])->find($contact);
        $contact->applicant?->setAttribute('recruited_at_formatted',
            $contact->applicant->recruited_at->format('d/m/Y h:i:s A'));
        return response()->json($contact);
    }
}