<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeInToPostmanHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_historys', function (Blueprint $table) {
            $table->string('type')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_doctor_logs', function (Blueprint $table) {
            $table->dropColumn('dev_task_id');
            $table->dropColumn('task_id');
        });
    }
}
