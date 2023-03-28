<?php

namespace Modules\Ruby\Http\Controllers\Manager;

use Modules\Ruby\Http\Controllers\ModuleController as BaseModuleController;
use Modules\Sapphire\Constants\Roles;

class ModuleController extends BaseModuleController{
    public function postLogin(){
        if(auth()->attempt(request()->only(['username', 'password']))
            && in_array(auth()->user()->role_id, [Roles::OWNER, Roles::HR_MANAGER, Roles::DIRECTOR]))
            return redirect()->intended('rm');
        if(!empty(auth()->user()))
            auth()->logout();
        return $this->getLogin(['fail' => true]);
    }
}