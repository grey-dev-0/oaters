<?php

namespace App\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateCommand;
use Stancl\Tenancy\Commands\Migrate;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Stancl\Tenancy\Events\MigratingDatabase;

class MigrateTenants extends Migrate{
    protected $description = 'Run migrations for tenant(s) respecting modules migration paths ordering';

    protected static function getTenantCommandName(): string{
        return 'module:tenants-migrate';
    }

    /**
     * Execute the console command.
     */
    public function handle(){
        $parameters = config('tenancy.migration_parameters');
        foreach($parameters as $parameter => $value){
            if($parameter != '--path' && !$this->input->hasParameterOption($parameter)){
                $this->input->setOption(ltrim($parameter, '-'), $value);
            }
        }

        if(!$this->confirmToProceed()){
            return;
        }

        tenancy()->runForMultiple($this->option('tenants'), function($tenant) use ($parameters){
            $this->line("Tenant: {$tenant->getTenantKey()}");

            event(new MigratingDatabase($tenant));

            // Migrating sorted module migration files for current tenant in loop.
            foreach($parameters['--path'] as $path){
                $this->input->setOption('path', $path);
                MigrateCommand::handle();
            }

            event(new DatabaseMigrated($tenant));
        });
    }
}
