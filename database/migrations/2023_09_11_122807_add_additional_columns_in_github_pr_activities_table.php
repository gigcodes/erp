<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
