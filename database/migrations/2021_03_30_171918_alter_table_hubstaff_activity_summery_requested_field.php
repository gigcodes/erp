<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableHubstaffActivitySummeryRequestedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            $table->integer('user_requested')->after('tracked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            $table->dropField('user_requested');
        });
    }
}
