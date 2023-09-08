<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentTextColumnInGithubPrActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->text('comment_text')->nullable();
            $table->dateTime('activity_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->dropColumn('comment_text');
            $table->dropColumn('activity_created_at');
        });
    }
}
