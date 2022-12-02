<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToLeadProductPriceCountlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_product_price_count_logs', function (Blueprint $table) {
            $table->string('original_price')->nullable();
            $table->string('promotion_per')->nullable();
            $table->string('promotion')->nullable();
            $table->string('segment_discount')->nullable();
            $table->string('segment_discount_per')->nullable();
            $table->string('total_price')->nullable();
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
            $table->dropColumn('original_price');
            $table->dropColumn('promotion_per');
            $table->dropColumn('promotion');
            $table->dropColumn('segment_discount');
            $table->dropColumn('segment_discount_per');
            $table->dropColumn('total_price');
        });
    }
}
