<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallerFieldsToTwilioDequeueCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_dequeue_calls', function (Blueprint $table) {
            $table->string('caller')->after('reservation_sid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_dequeue_calls', function (Blueprint $table) {
            $table->dropColumn('caller');
        });
    }
}
