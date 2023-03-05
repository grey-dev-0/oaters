<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RubyBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->create('r_salaries', function(Blueprint $table){
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['hourly', 'monthly', 'annual']);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_payroll_components', function(Blueprint $table){
            $table->increments('id');
            $table->enum('type', ['basic', 'allowance', 'incentive']);
        });

        Schema::connection('tenant')->create('r_payroll_component_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('payroll_component_id');
            $table->foreign('payroll_component_id')->references('id')->on('r_payroll_components')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('r_salary_components', function(Blueprint $table){
            $table->unsignedInteger('salary_id');
            $table->unsignedInteger('payroll_component_id');
            $table->primary(['salary_id', 'payroll_component_id']);
            $table->foreign('salary_id')->references('id')->on('r_salaries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('payroll_component_id')->references('id')->on('r_payroll_components')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
        });

        Schema::connection('tenant')->create('r_payroll_payments', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('salary_id')->nullable();
            $table->foreign('salary_id')->references('id')->on('r_salaries')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('le_bank_accounts')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedTinyInteger('units')->default(1);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_payment_components', function(Blueprint $table){
            $table->unsignedInteger('payroll_payment_id');
            $table->unsignedInteger('payroll_component_id');
            $table->primary(['payroll_payment_id', 'payroll_component_id'], 'pivot_primary_key');
            $table->foreign('payroll_payment_id', 'payment_id_foreign')->references('id')->on('r_payroll_payments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payroll_component_id', 'component_id_foreign')->references('id')->on('r_payroll_components')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
        });

        Schema::connection('tenant')->create('r_notices', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->foreign('author_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('payroll_payment_id')->nullable();
            $table->foreign('payroll_payment_id')->references('id')->on('r_payroll_payments')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['notice', 'warning', 'termination']);
            $table->text('content');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_leaves', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['sick', 'casual', 'annual']);
            $table->boolean('status')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_subordinates', function(Blueprint $table){
            $table->unsignedInteger('manager_id');
            $table->unsignedInteger('contact_id');
            $table->primary(['manager_id', 'contact_id']);
            $table->foreign('manager_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::connection('tenant')->create('r_punches', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->timestamp('created_at');
        });

        Schema::connection('tenant')->create('r_departments', function(Blueprint $table){
            $table->increments('id');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_department_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->foreign('department_id')->references('id')->on('r_departments')->onUpdate('cascade')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
        });

        Schema::connection('tenant')->create('r_vacancies', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->foreign('department_id')->references('id')->on('r_departments')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_vacancy_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('vacancy_id');
            $table->foreign('vacancy_id')->references('id')->on('r_vacancies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('title');
            $table->text('description');
        });

        Schema::connection('tenant')->create('r_degrees', function(Blueprint $table){
            $table->increments('id');
        });

        Schema::connection('tenant')->create('r_degree_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('degree_id');
            $table->foreign('degree_id')->references('id')->on('r_degrees')->onUpdate('cascade')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
        });

        Schema::connection('tenant')->create('r_applicants', function(Blueprint $table){
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('degree_id');
            $table->foreign('degree_id')->references('id')->on('r_degrees')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('degree_date');
            $table->unsignedTinyInteger('tenure');
            $table->timestamp('recruited_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('r_applicables', function(Blueprint $table){
            $table->unsignedInteger('applicant_id');
            $table->morphs('applicable');
            $table->primary(['applicant_id', 'applicable_type', 'applicable_id']);
            $table->foreign('applicant_id')->references('id')->on('r_applicants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::connection('tenant')->create('r_documents', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('r_applicants')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('filename');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('r_documents');
        Schema::connection('tenant')->dropIfExists('r_applicables');
        Schema::connection('tenant')->dropIfExists('r_applicants');
        Schema::connection('tenant')->dropIfExists('r_degree_locales');
        Schema::connection('tenant')->dropIfExists('r_degrees');
        Schema::connection('tenant')->dropIfExists('r_vacancy_locales');
        Schema::connection('tenant')->dropIfExists('r_vacancies');
        Schema::connection('tenant')->dropIfExists('r_department_locales');
        Schema::connection('tenant')->dropIfExists('r_departments');
        Schema::connection('tenant')->dropIfExists('r_punches');
        Schema::connection('tenant')->dropIfExists('r_subordinates');
        Schema::connection('tenant')->dropIfExists('r_leaves');
        Schema::connection('tenant')->dropIfExists('r_notices');
        Schema::connection('tenant')->dropIfExists('r_payment_components');
        Schema::connection('tenant')->dropIfExists('r_payroll_payments');
        Schema::connection('tenant')->dropIfExists('r_salary_components');
        Schema::connection('tenant')->dropIfExists('r_payroll_component_locales');
        Schema::connection('tenant')->dropIfExists('r_payroll_components');
        Schema::connection('tenant')->dropIfExists('r_salaries');
    }
}
