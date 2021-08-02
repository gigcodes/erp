<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderMonetaryAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("orders",function(Blueprint $table) {
            $table->integer("monetary_account_id")->nullable()->after("coupon_id");
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
        Schema::table("orders",function(Blueprint $table) {
            $table->dropField("monetary_account_id");
        });
    }
}
