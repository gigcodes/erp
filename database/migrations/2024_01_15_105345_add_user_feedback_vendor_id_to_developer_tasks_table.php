<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserFeedbackVendorIdToDeveloperTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->integer('user_feedback_vendor_id')->default(0)->after('user_feedback_cat_id');
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
            //
        });
    }
}
