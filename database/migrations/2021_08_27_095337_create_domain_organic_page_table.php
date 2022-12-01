<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainOrganicPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_organic_page', function (Blueprint $table) {
            $table->increments('id');
            $table->text('store_website_id');
            $table->text('tool_id');
            $table->text('database');
            $table->text('Url');
            $table->text('number_of_keywords');
            $table->text('traffic');
            $table->text('traffic_percentage');
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
        Schema::dropIfExists('domain_organic_page');
    }
}
