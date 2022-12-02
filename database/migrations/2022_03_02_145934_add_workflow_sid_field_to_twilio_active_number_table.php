<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowSidFieldToTwilioActiveNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_active_numbers', function (Blueprint $table) {
            $table->string('workflow_sid')->after('workspace_sid');
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
            $table->dropColumn('workflow_sid');
        });
    }
}
