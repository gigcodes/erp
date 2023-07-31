<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToMagentoFrontendParentFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_frontend_parent_folders', function (Blueprint $table) {
            $table->text('parent_image')->nullable();
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
        Schema::table('magento_frontend_parent_folders', function (Blueprint $table) {
            $table->dropColumn('MagentoFrontendParentFolder');
            $table->dropColumn('type');
        });
    }
}