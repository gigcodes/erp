<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoFrontendDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_frontend_docs', function (Blueprint $table) {
            $table->id();
            $table->integer('store_website_category_id');
            $table->text('location')->nullable();
            $table->text('admin_configuration')->nullable();
            $table->text('frontend_configuration')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('magento_frontend_docs');
    }
}
