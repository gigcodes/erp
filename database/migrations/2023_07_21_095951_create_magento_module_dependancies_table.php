<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoModuleDependanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_dependancies', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('magento_module_id');
            $table->text('depency_remark')->nullable();
            $table->text('depency_module_issues')->nullable();
            $table->text('depency_api_issues')->nullable();
            $table->text('depency_theme_issues')->nullable();
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
        Schema::dropIfExists('magento_module_dependancies');
    }
}
