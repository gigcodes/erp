<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserFeedbackVendorIdToUserFeedbackStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_feedback_status_updates', function (Blueprint $table) {
            $table->integer('user_feedback_vendor_id')->default(0)->after('user_feedback_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_feedback_status_updates', function (Blueprint $table) {
            //
        });
    }
}
