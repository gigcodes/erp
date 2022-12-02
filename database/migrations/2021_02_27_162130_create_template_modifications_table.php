<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_modifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->integer('row_index')->nullable();
            $table->string('tag')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();

            $table->foreign('template_id')
          ->references('id')->on('templates')
          ->onDelete('CASCADE')
          ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_modifications');
    }
}
