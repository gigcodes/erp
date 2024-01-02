<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseStatusIdColumnOnPurchaseProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_product_orders', function (Blueprint $table) {
            $table->integer('purchase_status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_product_orders', function (Blueprint $table) {
            $table->dropColumn('purchase_status_id');
        });
    }
}
