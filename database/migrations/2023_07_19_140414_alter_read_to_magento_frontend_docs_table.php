<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReadToMagentoFrontendDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->text('read')->nullable()->change();
            $table->text('write')->nullable()->change();
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
            $table->text('read')->change();
            $table->text('write')->change();
        });
    }
}
