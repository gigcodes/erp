<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_problems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('source');
            $table->string('test');
            $table->string('severity')->nullable();
            $table->string('type')->nullable();
            $table->text('error_body');
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_problems');
    }
}
