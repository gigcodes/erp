<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogRefidStoreMagentoApiSearchProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('store_magento_api_search_products', function (Blueprint $table) {
            $table->string('log_refid')->default('0')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('store_magento_api_search_products', function (Blueprint $table) {
            $table->dropColumn(['log_refid']);
        });
    }
}
