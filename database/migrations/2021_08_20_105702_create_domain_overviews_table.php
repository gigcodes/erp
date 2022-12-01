<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainOverviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_overviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            //$table->text('domain');
            $table->integer('rank');
            $table->integer('organic_keywords');
            $table->integer('organic_traffic');
            $table->integer('organic_cost');
            $table->integer('adwords_keywords');
            $table->integer('adwords_traffic');
            $table->integer('adwords_cost');
            $table->text('pla_keywords');
            $table->text('pla_uniques');
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
        Schema::dropIfExists('domain_overviews');
    }
}
