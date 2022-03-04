<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRenameTwilioWorkspaceIdInTwilioprioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_priorities', function (Blueprint $table) {
            $table->renameColumn('twilio_workspace_id', 'account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_priorities', function (Blueprint $table) {
            $table->renameColumn('account_id', 'twilio_workspace_id');
        });
    }
}
