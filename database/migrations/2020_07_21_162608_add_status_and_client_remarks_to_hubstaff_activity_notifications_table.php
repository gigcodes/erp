<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndClientRemarksToHubstaffActivityNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hubstaff_activity_notifications', function (Blueprint $table) {
            $table->integer('status');
            $table->string('client_remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hubstaff_activity_notifications', function (Blueprint $table) {
            //
        });
    }
}
