<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSopsIdToUserFeedbackCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_feedback_categories', function (Blueprint $table) {
            $table->integer('sops_id')->nulable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_feedback_categories', function (Blueprint $table) {
            $table->dropColumn('sops_id');
        });
    }
}
