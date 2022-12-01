<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCouponOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_coupon_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voucher_coupons_id')->nullable();
            $table->date('date_order_placed')->nullable();
            $table->string('order_no')->nullable();
            $table->string('order_amount')->nullable();
            $table->string('discount')->nullable();
            $table->string('final_amount')->nullable();
            $table->string('refund_amount')->nullable();
            $table->string('remark')->nullable();
            $table->string('user_id')->nullable();
            $table->string('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_coupon_orders');
    }
}
