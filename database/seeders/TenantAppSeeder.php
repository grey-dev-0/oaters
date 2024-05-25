<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\Database\Seeders\AddressSeeder;
use Modules\Common\Database\Seeders\ContactSeeder;
use Modules\Common\Database\Seeders\CountrySeeder;
use Modules\Common\Database\Seeders\EmailSeeder;
use Modules\Common\Database\Seeders\PhoneSeeder;
use Modules\Ruby\Database\Seeders\ApplicantSeeder;
use Modules\Ruby\Database\Seeders\DegreeSeeder;
use Modules\Ruby\Database\Seeders\DepartmentSeeder;
use Modules\Ruby\Database\Seeders\DocumentSeeder;
use Modules\Ruby\Database\Seeders\LeaveSeeder;
use Modules\Ruby\Database\Seeders\PermissionSeeder as RubyPermissionSeeder;
use Modules\Sapphire\Database\seeders\RoleSeeder;
use Modules\Sapphire\Database\seeders\UsersTableSeeder;

class TenantAppSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $this->call(CountrySeeder::class);
        $this->call(DegreeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RubyPermissionSeeder::class);
        if(!app()->isProduction()){
            $this->call(UsersTableSeeder::class);
            $this->call(ContactSeeder::class);
            $this->call(PhoneSeeder::class);
            $this->call(EmailSeeder::class);
            $this->call(AddressSeeder::class);
            $this->call(DepartmentSeeder::class);
            $this->call(ApplicantSeeder::class);
            $this->call(DocumentSeeder::class);
            $this->call(LeaveSeeder::class);
        }
    }
}
