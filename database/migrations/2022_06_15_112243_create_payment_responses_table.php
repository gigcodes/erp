<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_responses', function (Blueprint $table) {
            $table->engine = 'MyiSAM';
            $table->increments('id');
            $table->unsignedBigInteger('website_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('parent_id');
            $table->decimal('base_shipping_captured', 13, 4)->nullable();
            $table->decimal('shipping_captured', 13, 4)->nullable();
            $table->decimal('amount_refunded', 13, 4)->nullable();
            $table->decimal('base_amount_paid', 13, 4)->nullable();
            $table->decimal('amount_canceled', 13, 4)->nullable();
            $table->decimal('base_amount_authorized', 13, 4)->nullable();
            $table->decimal('base_amount_paid_online', 13, 4)->nullable();
            $table->decimal('base_amount_refunded_online', 13, 4)->nullable();
            $table->decimal('base_shipping_amount', 13, 4)->nullable();
            $table->decimal('shipping_amount', 13, 4)->nullable();
            $table->decimal('amount_paid', 13, 4)->nullable();
            $table->decimal('amount_authorized', 13, 4)->nullable();
            $table->decimal('base_amount_ordered', 13, 4)->nullable();
            $table->decimal('base_shipping_refunded', 13, 4)->nullable();
            $table->decimal('shipping_refunded', 13, 4)->nullable();
            $table->decimal('base_amount_refunded', 13, 4)->nullable();
            $table->decimal('amount_ordered', 13, 4)->nullable();
            $table->decimal('base_amount_canceled', 13, 4)->nullable();
            $table->decimal('quote_payment_id', 13, 4)->nullable();
            $table->integer('cc_exp_month')->nullable();
            $table->integer('cc_ss_start_year')->nullable();
            $table->string('cc_secure_verify')->nullable();
            $table->string('cc_approval')->nullable();
            $table->integer('cc_last_4')->nullable();
            $table->string('cc_type')->nullable();
            $table->integer('cc_exp_year')->nullable();
            $table->string('cc_status')->nullable();
            $table->foreign('website_id')->references('id')->on('store_websites')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('payment_responses');
    }
}
