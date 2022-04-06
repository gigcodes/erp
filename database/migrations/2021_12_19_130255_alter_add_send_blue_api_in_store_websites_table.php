<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddSendBlueApiInStoreWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->string('send_in_blue_account')->nullable()->after("description");
            $table->string('send_in_blue_api')->nullable()->after("send_in_blue_account");
            $table->string('send_in_blue_smtp_email_api')->nullable()->after("send_in_blue_api"); 
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->dropColumn('send_in_blue_api');
            $table->dropColumn('send_in_blue_account');
            $table->dropColumn('send_in_blue_sms_email_api');
        });
    }
}
