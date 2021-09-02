<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStoreMagentoApiSearchProductsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_magento_api_search_products', function($table) {
            $table->longText('category_names')->nullable()->after('composition');
            $table->longText('size_chart_url')->nullable()->after('composition');
            $table->longText('images')->nullable()->after('composition');
            $table->longText('status')->nullable();
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
    }
}
