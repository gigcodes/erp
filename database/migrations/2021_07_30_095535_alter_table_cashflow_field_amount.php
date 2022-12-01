<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableCashflowFieldAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement("ALTER TABLE `cash_flows` CHANGE `amount` `amount` DECIMAL(8.2) NOT NULL DEFAULT '0.00';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
