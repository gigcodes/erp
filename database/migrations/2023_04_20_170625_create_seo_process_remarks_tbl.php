<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoProcessRemarksTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_process_remarks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('seo_process_id')->nullable();
            $table->bigInteger('seo_process_status_id')->nullable();
            $table->string('remark')->nullable();
            $table->integer('index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_process_remarks');
    }
}
