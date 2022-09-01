<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->nullable();
            $table->string("parent_id")->nullable();
            $table->string("parent_id_type")->nullable();
            $table->string("parent_table")->nullable();
            $table->string("error")->nullable();
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
        Schema::dropIfExists('postman_errors');
    }
}
