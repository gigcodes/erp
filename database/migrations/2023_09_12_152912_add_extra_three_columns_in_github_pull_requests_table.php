<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraThreeColumnsInGithubPullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_pull_requests', function (Blueprint $table) {
            $table->string("source")->nullable();
            $table->string("destination")->nullable();
            $table->string("mergeable_state")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_pull_requests', function (Blueprint $table) {
            $table->dropColumn("source");
            $table->dropColumn("destination");
            $table->dropColumn("mergeable_state");
        });
    }
}
