<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoCssVariableValueHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_css_variable_value_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_css_variable_id');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_css_variable_value_histories');
    }
}
