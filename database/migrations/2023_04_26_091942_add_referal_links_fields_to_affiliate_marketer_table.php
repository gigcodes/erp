<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferalLinksFieldsToAffiliateMarketerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliates_marketers', function (Blueprint $table) {
            $table->text('referral_link')->nullable();
            $table->string('asset_id')->nullable();
            $table->string('source_id')->nullable();
            $table->boolean('approved')->default(false);
            $table->string('coupon')->nullable();
            $table->unsignedBigInteger('affiliate_programme_id')->nullable();
            $table->foreign('affiliate_programme_id')->references('id')->on('affiliate_programs')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliates_marketers', function (Blueprint $table) {
            $table->dropColumn('referral_link');
            $table->dropColumn('asset_id');
            $table->dropColumn('source_id');
            $table->dropColumn('approved');
            $table->dropColumn('coupon');
        });
    }
}
