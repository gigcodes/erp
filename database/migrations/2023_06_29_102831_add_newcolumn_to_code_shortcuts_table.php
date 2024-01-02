<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewcolumnToCodeShortcutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_shortcuts', function (Blueprint $table) {
            $table->integer('code_shortcuts_platform_id')->nullable();
            $table->text('title');
            $table->text('solution');
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
            $table->dropColumn('title');
            $table->dropColumn('solution');
            $table->dropColumn('code_shortcuts_platform_id');
        });
    }
}
