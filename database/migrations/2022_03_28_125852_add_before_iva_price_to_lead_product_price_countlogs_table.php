<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeforeIvaPriceToLeadProductPriceCountlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_product_price_count_logs', function (Blueprint $table) {
            $table->string('before_iva_product_price')->nullable();
            $table->string('euro_to_inr_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_product_price_count_logs', function (Blueprint $table) {
            $table->dropColumn('before_iva_product_price');
            $table->dropColumn('euro_to_inr_price');
        });
    }
}
