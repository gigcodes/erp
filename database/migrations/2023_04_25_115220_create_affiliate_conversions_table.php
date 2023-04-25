<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('affiliate_conversions')) {
            Schema::create('affiliate_conversions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_account_id');
                $table->string('external_id');
                $table->float('amount');
                $table->dateTime('click_date')->nullable();
                $table->string('click_referrer')->nullable();
                $table->string('click_landing_page')->nullable();
                $table->string('commission_id')->nullable();
                $table->string('program_id')->nullable();
                $table->string('affiliate_id')->nullable();
                $table->unsignedBigInteger('affiliate_commission_id')->nullable();
                $table->unsignedBigInteger('affiliate_program_id')->nullable();
                $table->unsignedBigInteger('affiliate_marketer_id')->nullable();
                $table->string('customer_id')->nullable();
                $table->string('customer_system_id')->nullable();
                $table->string('customer_status')->nullable();
                $table->text('meta_data')->nullable();
                $table->dateTime('commission_created_at')->nullable();
                $table->string('warnings')->nullable();
                $table->string('affiliate_meta_data')->nullable();
                $table->foreign('affiliate_account_id')->references('id')->on('affiliate_provider_accounts')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('affiliate_commission_id')->references('id')->on('affiliate_commissions')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('affiliate_program_id')->references('id')->on('affiliate_programs')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('affiliate_marketer_id')->references('id')->on('affiliates_marketers')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('affiliate_conversions');
    }
}
