<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsInGithubPrActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->string('action')->nullable();
            $table->text('body')->nullable();
            $table->text('description')->nullable();
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
            $table->dropColumn('action');
            $table->dropColumn('body');
            $table->dropColumn('description');
        });
    }
}
