<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugSeveritiesHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bug_severities_history', function (Blueprint $table) {
            $table->id();
            $table->integer('bug_id');
            $table->integer('old_severity_id')->nullable();
            $table->integer('severity_id');
            $table->integer('assign_to')->nullable();
            $table->integer('updated_by');
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
        Schema::dropIfExists('bug_severities_history');
    }
}
