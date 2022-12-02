<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodeRuleLogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('coupon_code_rule_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rule_id')->index();
            $table->string('coupon_code')->nullable();
            $table->string('log_type')->nullable();
            $table->text('message');
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
        //
        Schema::dropIfExists('coupon_code_rule_logs');
    }
}
