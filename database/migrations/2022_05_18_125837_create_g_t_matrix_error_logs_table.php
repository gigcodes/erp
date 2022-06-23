<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGTMatrixErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_t_matrix_error_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_viewGTM_id')->nullable();
            $table->string('error_type')->nullable();
            $table->string('error_title')->nullable();
            $table->longText('error')->nullable();
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
        Schema::dropIfExists('g_t_matrix_error_logs');
    }
}
