<?php

namespace App\Traits;

/**
 * @method \Modules\Sapphire\Entities\Tenant connect() Switches tenant's database connection to this tenant's database.
 */
trait InitializesTenantDatabase{
    /**
     * Switches tenant's database connection from the main database to the provided tenant's.
     *
     * @param \Modules\Sapphire\Entities\Tenant $tenant The tenant to connect to their database.
     * @return void
     */
    private function reconnect($tenant){
        \DB::purge('tenant');
        $configPrefix = 'database.connections.tenant';
        config([
            "$configPrefix.database" => $tenant->subdomain,
            "$configPrefix.username" => $tenant->subdomain,
            "$configPrefix.password" => $tenant->hash
        ]);
        \DB::reconnect('tenant');
        \Schema::connection('tenant')->getConnection()->reconnect();
    }

    /**
     * @inheritdoc
     */
    public function __call($function, $arguments){
        if($function == 'connect'){
            $this->reconnect($this);
            return $this;
        }
        return parent::__call($function, $arguments);
    }
}