<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCashflowDueAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("cash_flows",function(Blueprint $table) {
            $table->double("due_amount_eur")->default(0.00)->after("amount_eur");
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
        Schema::table("cash_flows",function(Blueprint $table) {
            $table->dropField("due_amount_eur");
        });
    }
}
