<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwilioRecoveryCodeInTwilioCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_credentials', function (Blueprint $table) {
            $table->string('twilio_recovery_code')->nullable()->after('twiml_app_sid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_credentials', function (Blueprint $table) {
            //
        });
    }
}
