<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUichecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uichecks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_development_id')->nullable();
            $table->integer('site_development_category_id')->nullable();
            $table->integer('website_id')->nullable();
            $table->string('issue')->nullable();
            $table->string('communication_message')->nullable();
            $table->string('dev_status_id')->nullable();
            $table->string('admin_status_id')->nullable();
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
        Schema::dropIfExists('uichecks');
    }
}
