<?php

namespace App\Traits;

trait UsesTenantDatabase{
    public function initializeUsesTenantDatabase(){
        $this->connection = 'tenant';
    }
}