<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwilioCredentialIdToTwilioActiveNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_active_numbers', function (Blueprint $table) {
            $table->unsignedInteger('twilio_credential_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_active_numbers', function (Blueprint $table) {
            $table->dropColumn('twilio_credential_id');
        });
    }
}
