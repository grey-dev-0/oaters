<?php

namespace Modules\Ruby\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller{
    public function getIndex(){
        return view('ruby::manager.dashboard');
    }
}
