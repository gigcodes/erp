<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeParentAccountSidNullableOnTwilioWebhookErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_webhook_errors', function (Blueprint $table) {
            $table->text('parent_account_sid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_webhook_errors', function (Blueprint $table) {
            $table->text('parent_account_sid')->nullable(false)->change();
        });
    }
}
