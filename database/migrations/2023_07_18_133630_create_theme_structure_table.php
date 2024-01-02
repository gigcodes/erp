<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_structure', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('theme_id')->nullable();
            $table->string('name');
            $table->boolean('is_file');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('position')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('theme_structure')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theme_structure');
    }
}
