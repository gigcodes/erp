<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserFeedbackCategoryToSop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_feedback_categories', function (Blueprint $table) {
            $table->text('sop_id')->nullable();
            $table->text('sop')->nullable();
            $table->string('sub_cat')->nullable();
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
            $table->dropColumn('sop');
            $table->dropColumn('sub_cat');
        });
    }
}
