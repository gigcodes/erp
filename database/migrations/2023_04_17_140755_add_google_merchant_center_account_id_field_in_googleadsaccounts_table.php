<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogleMerchantCenterAccountIdFieldInGoogleadsaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            $table->string('google_merchant_center_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            $table->dropColumn('google_merchant_center_account_id');
        });
    }
}
