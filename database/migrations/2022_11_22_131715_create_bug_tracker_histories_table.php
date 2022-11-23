<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBugTrackerHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bug_tracker_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bug_id');
            $table->integer('bug_type_id')->nullable();
            $table->string('summary')->nullable();
            $table->string('step_to_reproduce')->nullable();
            $table->string('url')->nullable();
            $table->integer('bug_environment_id')->nullable();
            $table->integer('assign_to')->nullable();
            $table->integer('bug_severity_id')->nullable();
            $table->integer('bug_status_id')->nullable();
            $table->string('module_id')->nullable();
            $table->longText('remark')->nullable();
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
        Schema::dropIfExists('bug_tracker_histories');
    }
}
