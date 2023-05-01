<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('affiliate_payments')) {
            Schema::create('affiliate_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_account_id');
                $table->string('payment_id');
                $table->dateTime('payment_created_at');
                $table->unsignedBigInteger('affiliate_marketer_id');
                $table->float('amount');
                $table->string('currency');
                $table->foreign('affiliate_account_id')->references('id')->on('affiliate_provider_accounts')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('affiliate_marketer_id')->references('id')->on('affiliates_marketers')->cascadeOnDelete()->cascadeOnUpdate();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_payments');
    }
}
