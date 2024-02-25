<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapperLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapper_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('scrapper_id')->default(0);
            $table->integer('task_id')->default(0);
            $table->string('task_type')->nullable();
            $table->string('log')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('scrapper_logs');
    }
}
