<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProjectFileManagersHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_file_managers_history',function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_id');
            $table->string('name')->nullable();
            $table->string('old_size')->nullable();
            $table->string('new_size')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('project_file_managers_history');
    }
}
