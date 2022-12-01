<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bug_trackers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bug_type_id');
            $table->string('summary');
            $table->string('step_to_reproduce')->nullable();
            $table->string('url')->nullable();
            $table->integer('bug_environment_id');
            $table->integer('assign_to');
            $table->integer('bug_severity_id');
            $table->integer('bug_status_id');
            $table->string('module_id');
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
        Schema::dropIfExists('bug_trackers');
    }
}
