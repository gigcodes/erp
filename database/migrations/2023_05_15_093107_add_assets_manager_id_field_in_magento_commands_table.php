<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssetsManagerIdFieldInMagentoCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_commands', function (Blueprint $table) {
             $table->unsignedBigInteger("assets_manager_id")->nullable()->after('website_ids');
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
            $table->dropColumn("assets_manager_id");
        });
    }
}
