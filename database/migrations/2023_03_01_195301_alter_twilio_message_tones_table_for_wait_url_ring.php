<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTwilioMessageTonesTableForWaitUrlRing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_message_tones', function (Blueprint $table) {
            $table->string('wait_url_ring')->nullable()->after('busy_ring');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_message_tones', function(Blueprint $table) {
            $table->dropColumn('wait_url_ring');
        });
    }
}
