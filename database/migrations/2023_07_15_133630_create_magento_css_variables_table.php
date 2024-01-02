<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoCssVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_css_variables', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->string('filename')->nullable();
            $table->string('file_path')->nullable();
            $table->string('variable')->nullable();
            $table->string('value')->nullable();
            $table->integer('create_by')->nullable();
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
        Schema::dropIfExists('magento_css_variables');
    }
}
