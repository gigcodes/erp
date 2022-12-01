<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('url_organic_search_keywords');
            $table->text('url_paid_search_keywords');
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
        Schema::dropIfExists('url_reports');
    }
}
