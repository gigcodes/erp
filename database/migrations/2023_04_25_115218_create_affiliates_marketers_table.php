<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliatesMarketersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates_marketers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_account_id');
            $table->string('affiliate_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->string('company_name')->nullable();
            $table->string('company_description')->nullable();
            $table->string('address_one')->nullable();
            $table->string('address_two')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_country_code')->nullable();
            $table->string('address_country_name')->nullable();
            $table->text('meta_data')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('affiliate_created_at')->nullable();
            $table->unsignedBigInteger('affiliate_group_id')->nullable();
            $table->dateTime('promoted_at')->nullable();
            $table->string('promotion_method')->nullable();
            $table->foreign('affiliate_account_id')->references('id')->on('affiliate_provider_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('affiliate_group_id')->references('id')->on('affiliate_groups')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('affiliates_marketers');
    }
}
