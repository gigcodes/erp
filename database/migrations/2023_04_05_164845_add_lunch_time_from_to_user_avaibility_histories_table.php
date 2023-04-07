<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLunchTimeFromToUserAvaibilityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_avaibility_histories', function (Blueprint $table) {
            $table->time('lunch_time_from')->nullable()->after('lunch_time');
            $table->time('lunch_time_to')->nullable()->after('lunch_time_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_avaibility_histories', function (Blueprint $table) {
            $table->dropColumn('lunch_time_from');
            $table->dropColumn('lunch_time_to');
        });
    }
}
