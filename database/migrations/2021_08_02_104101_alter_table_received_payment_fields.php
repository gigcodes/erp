<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableReceivedPaymentFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->double('erp_amount', 8, 2)->default('0.00')->after('amount');
            $table->double('erp_eur_amount', 8, 2)->default('0.00')->after('erp_amount');
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
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropFields('erp_amount');
            $table->dropFields('erp_eur_amount');
        });
    }
}
