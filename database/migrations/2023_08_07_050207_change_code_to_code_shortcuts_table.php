<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCodeToCodeShortcutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_shortcuts', function (Blueprint $table) {
            $table->integer('supplier_id')->nullable()->change();
            $table->string('code')->nullable()->change();
            $table->string('description')->nullable()->change();
            $table->text('title')->nullable()->change();
            $table->text('solution')->nullable()->change();
            $table->integer('website_log_view_id')->nullable();
            $table->integer('jenkins_log_id')->nullable();
            $table->text('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('code_shortcuts', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
            $table->dropColumn('code');
            $table->dropColumn('description');
            $table->dropColumn('title');
            $table->dropColumn('solution');
            $table->dropColumn('website_log_view_id');
            $table->dropColumn('jenkins_log_id');
            $table->dropColumn('type');
        });
    }
}
