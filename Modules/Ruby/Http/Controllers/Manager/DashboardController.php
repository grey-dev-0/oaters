<?php

namespace Modules\Ruby\Http\Controllers\Manager;

use Illuminate\Routing\Controller;

class DashboardController extends Controller{
    public function getIndex(){
        return view('ruby::manager.dashboard');
    }
}
