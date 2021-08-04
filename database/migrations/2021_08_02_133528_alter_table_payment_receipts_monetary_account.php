<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePaymentReceiptsMonetaryAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("payment_receipts",function(Blueprint $table) {
            $table->integer("monetary_account_id")->nullable()->after("billing_due_date");
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
        Schema::table("payment_receipts",function(Blueprint $table) {
            $table->dropField("monetary_account_id");
        });
    }
}
