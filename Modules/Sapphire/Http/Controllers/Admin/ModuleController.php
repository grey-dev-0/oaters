<?php

namespace Modules\Sapphire\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class ModuleController extends Controller{
    public function postLogin(){
        if(auth('admin')->attempt(request()->only(['email', 'password']) + ['main' => true]))
            return redirect()->intended('sa');
        return view('sapphire::login', ['fail' => true]);
    }

    public function getLogout(){
        auth('admin')->logout();
        return redirect('/');
    }
}
