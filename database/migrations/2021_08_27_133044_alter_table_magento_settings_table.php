<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMagentoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('magento_settings', function (Blueprint $table) {
            $table->integer('store_website_id')->nullable()->after('scope_id');
            $table->integer('website_store_id')->nullable()->after('store_website_id');
            $table->integer('website_store_view_id')->nullable()->after('website_store_id');
            $table->string('data_type')->nullable()->after('website_store_view_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('magento_settings', function (Blueprint $table) {
            $table->dropField('store_website_id');
            $table->dropField('website_store_id');
            $table->dropField('website_store_view_id');
        });
    }
}
