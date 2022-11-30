<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastInventoryAtColumnInScrapedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraped_products', function (Blueprint $table) {
            $table->dateTime('last_inventory_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraped_products', function (Blueprint $table) {
            $table->dropColumn('last_inventory_at');
        });
    }
}
