<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('suite');
            $table->string('created_by')->nullable();
            $table->string('module_id');
            $table->longText('precondition');
            $table->bigInteger('assign_to');
            $table->longText('step_to_reproduce');
            $table->longText('expected_result');
            $table->integer('test_status_id');
            $table->integer('updated_by')->nullable();
            $table->bigInteger('website')->nullable();
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
        Schema::dropIfExists('test_cases');
    }
}
