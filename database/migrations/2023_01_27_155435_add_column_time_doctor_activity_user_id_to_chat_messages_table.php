<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('time_doctor_activity_summary_id')->after('hubstaff_activity_summary_id');
            $table->string('time_doctor_activity_user_id')->after('hubstuff_activity_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('time_doctor_activity_summary_id')->after('hubstaff_activity_summary_id');
            $table->dropColumn('time_doctor_activity_summary_id')->after('hubstuff_activity_user_id');
        });
    }
};
