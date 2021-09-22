<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTwillioNumberWorkspaceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('twilio_active_numbers',function(Blueprint $table) {
            $table->string("workspace_sid")->nullable()->after("phone_number");
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
        Schema::table('twilio_active_numbers',function(Blueprint $table) {
            $table->dropField("workspace_sid");
        });
    }
}