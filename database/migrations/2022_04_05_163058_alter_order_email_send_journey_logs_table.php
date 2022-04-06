<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrderEmailSendJourneyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_email_send_journey_logs', function (Blueprint $table) {
            $table->longText('message')->change();
            $table->longText('template')->change();
            $table->longText('error_msg')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_email_send_journey_logs', function (Blueprint $table) {
            $table->string('message')->change();
            $table->string('template')->change();
            $table->string('error_msg')->change();
        });
    }
}
