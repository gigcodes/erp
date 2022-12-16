<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTestCaseHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_case_histories', function (Blueprint $table) {
            $table->bigInteger('test_case_id')->nullable()->change();
            $table->bigInteger('bug_id')->nullable();
            $table->string('name')->nullable()->change();
            $table->string('suite')->nullable()->change();
            $table->string('module_id')->nullable()->change();
            $table->longText('precondition')->nullable()->change();
            $table->bigInteger('assign_to')->nullable()->change();
            $table->longText('step_to_reproduce')->nullable()->change();
            $table->longText('expected_result')->nullable()->change();
            $table->integer('test_status_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_case_histories', function (Blueprint $table) {
            $table->dropColumn('bug_id');
        });
    }
}
