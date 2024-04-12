<?php

namespace Modules\Sapphire\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class ModuleController extends Controller{
    public function postLogin(){
        if(auth('admin')->attempt(request()->only(['email', 'password']))){
            if(auth('admin')->user()->hasRole('master'))
                return redirect()->intended('sa');
            else
                auth()->logout();
        }
        return view('sapphire::login', ['fail' => true]);
    }

    public function getLogout(){
        auth('admin')->logout();
        return redirect('/');
    }
}
