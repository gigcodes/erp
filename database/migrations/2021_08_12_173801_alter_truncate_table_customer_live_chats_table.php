<?php

use Illuminate\Database\Migrations\Migration;

class AlterTruncateTableCustomerLiveChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::table('customer_live_chats')->truncate();
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
