<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Jobs\MigrateDatabase;

class MigrateTenants extends MigrateDatabase{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        Artisan::call('module:tenants-migrate', [
            '--tenants' => [$this->tenant->getTenantKey()],
        ]);
    }
}