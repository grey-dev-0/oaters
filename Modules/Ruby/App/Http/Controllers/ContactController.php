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
        return \DataTables::of(Contact::activeRecruit()->withRoles()->withDefaultInfo()->select(['id', 'name', 'job']))->make();
    }
}