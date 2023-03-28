<?php

namespace Modules\Ruby\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Sapphire\Constants\Roles;

class ModuleController extends Controller{
    public function getLogin($extraData = []){
        $data = ['title' => 'Ruby', 'borderColor' => 'red-4', 'buttonColor' => 'danger'];
        if(!empty($extraData))
            $data += $extraData;
        return view('common::login', $data);
    }

    public function postLogin(){
        if(auth()->attempt(request()->only(['username', 'password'])) && Roles::inSystemRoles(auth()->user()->role_id)
            && auth()->user()->accesses('ruby'))
            return redirect()->intended('r');
        if(!empty(auth()->user()))
            auth()->logout();
        return $this->getLogin(['fail' => true]);
    }
}
