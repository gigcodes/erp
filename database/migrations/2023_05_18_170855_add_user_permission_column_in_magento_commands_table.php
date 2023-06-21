<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserPermissionColumnInMagentoCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_commands', function (Blueprint $table) {
            $table->mediumText('user_permission')->nullable()->after('response');
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
            //
        });
    }
}
