<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperTaskStatusChecklistRemarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_task_status_checklist_remarks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->integer('developer_task_status_checklist_id')->nullable();
            $table->string('remark',1024)->nullable();
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
        Schema::dropIfExists('developer_task_status_checklist_remarks');
    }
}
