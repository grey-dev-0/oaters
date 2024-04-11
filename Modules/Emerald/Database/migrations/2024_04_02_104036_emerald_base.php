<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('e_milestones', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();
        });

        Schema::create('e_tasks', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('e_tasks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('milestone_id')->nullable();
            $table->foreign('milestone_id')->references('id')->on('e_milestones')->onUpdate('cascade')->onDelete('set null');
            $table->integer('estimated_time')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('e_attachments', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('e_tasks')->onUpdate('cascade')->onDelete('cascade');
            $table->string('filename');
        });

        Schema::create('e_attachment_versions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('attachment_id');
            $table->foreign('attachment_id')->references('id')->on('e_attachments')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('e_comments', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('e_tasks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('set null');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('e_assignees', function(Blueprint $table){
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('task_id');
            $table->primary(['contact_id', 'task_id']);
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('e_tasks')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('e_labels', function(Blueprint $table){
            $table->increments('id');
            $table->enum('function', ['type', 'workflow', 'tag']);
            $table->boolean('system')->default(false);
            $table->timestamps();
        });

        Schema::create('e_label_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('label_id');
            $table->foreign('label_id')->references('id')->on('e_labels')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('locale', 2);
        });

        Schema::create('e_task_labels', function(Blueprint $table){
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('label_id');
            $table->primary(['task_id', 'label_id']);
            $table->foreign('task_id')->references('id')->on('e_tasks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('label_id')->references('id')->on('e_labels')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('e_workflows', function(Blueprint $table){
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('e_labels')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('system')->default(false);
            $table->text('label_ids');
        });

        Schema::create('e_custom_schedules', function(Blueprint $table){
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->text('working_days');
            $table->string('start_time', 5);
            $table->string('end_time', 5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('e_custom_schedules');
        Schema::dropIfExists('e_workflows');
        Schema::dropIfExists('e_task_labels');
        Schema::dropIfExists('e_label_locales');
        Schema::dropIfExists('e_labels');
        Schema::dropIfExists('e_assignees');
        Schema::dropIfExists('e_comments');
        Schema::dropIfExists('e_attachment_versions');
        Schema::dropIfExists('e_attachments');
        Schema::dropIfExists('e_tasks');
        Schema::dropIfExists('e_milestones');
    }
};
