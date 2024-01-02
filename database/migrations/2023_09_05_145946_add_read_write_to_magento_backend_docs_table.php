<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadWriteToMagentoBackendDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_backend_docs', function (Blueprint $table) {
            $table->text('read')->nullable();
            $table->text('write')->nullable();
            $table->text('admin_config_google_drive_file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_backend_docs', function (Blueprint $table) {
            $table->dropColumn('read');
            $table->dropColumn('write');
            $table->dropColumn('admin_config_google_drive_file_id');
        });
    }
}
