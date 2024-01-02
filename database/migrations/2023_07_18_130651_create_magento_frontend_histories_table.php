<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoFrontendHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_frontend_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_frontend_docs_id');
            $table->integer('store_website_category_id');
            $table->text('location')->nullable();
            $table->text('admin_configuration')->nullable();
            $table->text('frontend_configuration')->nullable();
            $table->integer('updated_by');

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
        Schema::dropIfExists('magento_frontend_histories');
    }
}
