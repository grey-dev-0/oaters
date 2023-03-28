<?php

namespace Modules\Ruby\Http\Controllers;

use Illuminate\Routing\Controller;

class ModuleController extends Controller{
    public function getLogin($extraData = []){
        $data = ['title' => 'Ruby', 'borderColor' => 'red-4', 'buttonColor' => 'danger'];
        return view('ruby::login', $data);
    }
}
