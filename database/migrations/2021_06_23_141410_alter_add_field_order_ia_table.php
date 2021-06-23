<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddFieldOrderIaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('purchase_product_orders',function(Blueprint $table) {
            $table->string("order_products_order_id")->nullable();
            $table->string('order_products_id', 191)->change();
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
        Schema::table('purchase_product_orders',function(Blueprint $table) {
            $table->dropField("order_products_order_id");
        });
    }
}
