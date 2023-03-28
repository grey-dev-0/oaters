<?php

namespace Modules\Sapphire\Http\Controllers\User;

use Illuminate\Routing\Controller;
use Modules\Sapphire\Constants\Roles;

class ModuleController extends Controller{
    public function postLogin(){
        if(auth()->attempt(request()->only(['username', 'password'])) && Roles::inSystemRoles(auth()->user()->role_id))
            return redirect()->intended('portal');
        if(!empty(auth()->user()))
            auth()->logout();
        return view('sapphire::login', ['fail' => true]);
    }

    public function getLogout(){
        auth()->logout();
        return redirect('/');
    }
}
