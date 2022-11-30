<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableScrapedProductsAddColumnCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraped_products', function (Blueprint $table) {
            $table->string('currency', 3)->after('images');
            $table->string('size_system', 2)->after('discounted_price_eur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraped_products', function ($table) {
            $table->dropColumn('currency');
            $table->dropColumn('size_system');
        });
    }
}
