<?php

namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Common\App\Models\Contact;

class ContactController extends Controller{
    public function postSearch($type){
        $rolesGroup = ($type == 'managers')?
            ['general-manager', 'financial-manager', 'hr-manager'] :
            ['accountant', 'hr-assistant', 'employee'];
        $rolesGroup = array_merge($rolesGroup, ['director', 'team-lead']);
        $suggestions= Contact::where(fn($q) => $q->where('name', 'like', '%'.($query = request('query')).'%')->orWhereHas('emails', fn($emails) => $emails->where('address', 'like', "%$query%")))->whereHas('roles', fn($roles) => $roles->whereIn('name', $rolesGroup))->with(['emails' => fn($emails) => $emails->whereDefault(true)])->limit(request('limit'))->get(['id', 'name'])->map(fn($contact) => $contact->only('id') + ['name' => "{$contact->name} - {$contact->emails->first()?->address}"])->pluck('name', 'id');
        return response()->json(compact('suggestions'));
    }
}