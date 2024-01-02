<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentFolderToMagentoFrontendDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->text('parent_folder')->nullable();
            $table->text('child_folder')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->dropColumn('parent_folder');
            $table->dropColumn('child_folder');
        });
    }
}
