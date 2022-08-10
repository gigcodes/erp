<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_coupon_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voucher_coupons_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->date('valid_date')->nullable();
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
        Schema::dropIfExists('voucher_coupon_codes');
    }
}
