<?php

namespace Modules\Sapphire\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class PurchaseController extends Controller{
    public function postIndex(){
        $response = \DataTables::of();
        # TODO: A lot of backend work!!
        return $response;
    }
}
