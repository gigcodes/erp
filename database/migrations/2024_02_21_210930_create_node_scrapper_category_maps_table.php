<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeScrapperCategoryMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_scrapper_category_maps', function (Blueprint $table) {
            $table->id();
            $table->json('category_stack')->nullable();
            $table->json('product_urls')->nullable();
            $table->string('supplier')->nullable();
            $table->json('mapped_categories')->nullable();
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
        Schema::dropIfExists('node_scrapper_category_maps');
    }
}
