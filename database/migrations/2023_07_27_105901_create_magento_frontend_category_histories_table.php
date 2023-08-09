<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoFrontendCategoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_frontend_category_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_frontend_docs_id');
            $table->integer('old_category_id')->nullable();
            $table->integer('new_category_id')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_frontend_category_histories');
    }
}
