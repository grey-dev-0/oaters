<?php

namespace Modules\Ruby\App\Http\Controllers;

use Illuminate\Routing\Controller;

class ModuleController extends Controller{
    public function getLogin($extraData = []){
        $data = ['title' => 'Ruby', 'borderColor' => 'red-4', 'buttonColor' => 'danger'];
        if(!empty($extraData))
            $data += $extraData;
        return view('common::login', $data);
    }

    public function postLogin(){
        if(auth()->attempt(request()->only(['username', 'password'])) && auth()->user()->can('ruby'))
            return redirect()->intended('r');
        if(!empty(auth()->user()))
            auth()->logout();
        return $this->getLogin(['fail' => true]);
    }
}
