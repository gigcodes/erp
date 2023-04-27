<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatesCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('affiliates_customers')) {
            Schema::create('affiliates_customers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_account_id');
                $table->string('customer_id');
                $table->string('customer_system_id');
                $table->string('status');
                $table->dateTime('customer_created_at');
                $table->dateTime('click_date')->nullable();
                $table->string('click_referrer')->nullable();
                $table->string('click_landing_page')->nullable();
                $table->string('program_id')->nullable();
                $table->string('affiliate_id')->nullable();
                $table->unsignedBigInteger('affiliate_program_id')->nullable();
                $table->unsignedBigInteger('affiliate_marketer_id')->nullable();
                $table->string('affiliate_meta_data')->nullable();
                $table->text('meta_data')->nullable();
                $table->string('warnings')->nullable();
                $table->foreign('affiliate_account_id')->references('id')->on('affiliate_provider_accounts')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('affiliates_customers');
    }
}
