<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddAppSidTwilioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('twilio_credentials',function(Blueprint $table) {
            $table->string('twiml_app_sid')->nullable()->after('status');
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
        Schema::table('twilio_credentials',function(Blueprint $table) {
            $table->dropField('twiml_app_sid');
        });
    }
}
