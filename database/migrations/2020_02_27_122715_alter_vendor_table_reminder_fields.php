<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVendorTableReminderFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->integer('reminder_last_reply')->default(1)->after('frequency');
            $table->timestamp('reminder_from')->default('0000-00-00 00:00:00')->after('reminder_last_reply');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('reminder_last_reply');
            $table->dropColumn('reminder_from');
        });
    }
}
