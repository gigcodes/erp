<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserFeedbackIdToDeveloperTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->integer('user_feedback_cat_id')->nulable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropColumn('user_feedback_cat_id');
        });
    }
}
