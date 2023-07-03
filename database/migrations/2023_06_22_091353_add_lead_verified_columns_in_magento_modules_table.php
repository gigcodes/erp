<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadVerifiedColumnsInMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->text('dev_last_remark')->nullable()->after('last_message');
            $table->text('lead_last_remark')->nullable()->after('dev_last_remark');
            $table->integer('lead_verified_by')->nullable()->after('dev_verified_status_id');
            $table->integer('lead_verified_status_id')->nullable()->after('lead_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('dev_last_remark');
            $table->dropColumn('lead_last_remark');
            $table->dropColumn('lead_verified_by');
            $table->dropColumn('lead_verified_status_id');
        });
    }
}
