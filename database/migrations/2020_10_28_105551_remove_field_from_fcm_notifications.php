<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldFromFcmNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_fcm_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('push_fcm_notifications', 'token')) {
                $table->dropColumn('token');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_fcm_notifications', function (Blueprint $table) {
            //
        });
    }
}
