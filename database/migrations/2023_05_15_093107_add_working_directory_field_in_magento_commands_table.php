<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkingDirectoryFieldInMagentoCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_commands', function (Blueprint $table) {
            $table->string("working_directory")->nullable()->after('command_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_commands', function (Blueprint $table) {
            $table->string("working_directory");
        });
    }
}
