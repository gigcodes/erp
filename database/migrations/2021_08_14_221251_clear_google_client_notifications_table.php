<?php

use App\GoogleClientNotification;
use Illuminate\Database\Migrations\Migration;

class ClearGoogleClientNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        GoogleClientNotification::truncate();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
