<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMagentoSettingDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        if (! Schema::hasColumn('magento_settings', 'data_type')) {
            Schema::table('magento_settings', function (Blueprint $table) {
                $table->string('data_type')->nullable()->after('website_store_view_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
