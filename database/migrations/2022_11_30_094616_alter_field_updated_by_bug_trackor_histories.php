<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldUpdatedByBugTrackorHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bug_tracker_histories', function (Blueprint $table) {
            $table->text('summary')->change();
            $table->text('step_to_reproduce')->change();
            $table->bigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bug_tracker_histories', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
}
