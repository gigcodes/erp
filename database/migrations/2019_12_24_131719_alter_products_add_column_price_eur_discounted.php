<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsAddColumnPriceEurDiscounted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $table)
        {
            $table->double('price_eur_discounted')->after('price_eur_special')->default(0);
            $table->double('price_inr_discounted')->after('price_special')->default(0);
            $table->renameColumn('price_special', 'price_inr_special');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price_eur_discounted');
            $table->dropColumn('price_inr_discounted');
            $table->renameColumn('price_inr_special', 'price_special');
        });
    }
}
