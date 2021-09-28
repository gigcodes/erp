<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBuildProcessHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('build_process_histories', function (Blueprint $table) {
            $table->increments('id');             
            $table->integer('store_website_id');
            $table->integer('created_by');
            $table->integer('build_number');
            $table->enum('status', ['running', 'success', 'failure'])->default('running')->index();
            $table->text('text');
            $table->text('build_name');
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
        //
    }
}
