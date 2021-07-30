<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderProductCurrencyField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_products',function(Blueprint $table) {
            $table->string("currency")->default("EUR")->after("product_price");
            $table->float("eur_price")->default("0.00")->after("currency");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products',function(Blueprint $table) {
            $table->dropField("currency");
            $table->dropField("eur_price");
        });
    }
}
