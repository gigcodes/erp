<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdAgainToScheduledMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scheduled_messages', function (Blueprint $table) {
            $table->integer('customer_id')->nullable()->after('user_id');

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_messages', function (Blueprint $table) {
            $table->dropForeign('scheduled_messages_customer_id_foreign');
            $table->dropColumn('customer_id');
        });
    }
}
