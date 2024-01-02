<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildPrInBuildProcessHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('build_process_histories', function (Blueprint $table) {
            $table->string('build_pr')->after('github_branch_state_name')->nullable();
            $table->string('initiate_from')->after('build_pr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('build_process_histories', function (Blueprint $table) {
            $table->dropColumn('build_pr');
            $table->dropColumn('initiate_from');
        });
    }
}
