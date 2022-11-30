<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableScrapedProductMissingLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraped_product_missing_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('website')->nullable();
            $table->string('supplier')->nullable();
            $table->integer('total_product')->default(0);
            $table->integer('missing_category')->default(0);
            $table->integer('missing_color')->default(0);
            $table->integer('missing_composition')->default(0);
            $table->integer('missing_name')->default(0);
            $table->integer('missing_short_description')->default(0);
            $table->integer('missing_price')->default(0);
            $table->integer('missing_size')->default(0);
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
        Schema::dropIfExists('scraped_product_missing_log');
    }
}
