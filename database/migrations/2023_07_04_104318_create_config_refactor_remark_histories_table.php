<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigRefactorRemarkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_refactor_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('config_refactor_id');
            $table->string('column_name');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('config_refactor_remark_histories');
    }
}
