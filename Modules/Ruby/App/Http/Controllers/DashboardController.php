<?php

namespace Modules\Ruby\App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller{
    public function getIndex(){
        return view('ruby::dashboard');
    }
}
