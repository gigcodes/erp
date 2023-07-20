<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelColumnsInGithubPrActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->string("label_name")->nullable();
            $table->string("label_color")->nullable();
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
            $table->dropColumn("label_name");
            $table->dropColumn("label_color");
        });
    }
}
