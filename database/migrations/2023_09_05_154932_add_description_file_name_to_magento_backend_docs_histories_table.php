<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionFileNameToMagentoBackendDocsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_backend_docs_histories', function (Blueprint $table) {
            $table->text('file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_backend_docs_histories', function (Blueprint $table) {
            $table->dropColumn('file_name');
        });
    }
}
