<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateProviderAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('affiliate_provider_accounts')) {
            Schema::create('affiliate_provider_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliates_provider_id');
                $table->integer('store_website_id');
                $table->text('api_key');
                $table->boolean('status');
                $table->timestamps();
                $table->foreign('affiliates_provider_id')->references('id')->on('affiliate_providers')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('store_website_id')->references('id')->on('store_websites')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('affiliate_provider_accounts');
    }
}
