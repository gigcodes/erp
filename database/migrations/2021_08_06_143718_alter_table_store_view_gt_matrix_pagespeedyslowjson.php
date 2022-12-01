<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStoreViewGtMatrixPagespeedyslowjson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('store_views_gt_metrix', function (Blueprint $table) {
            $table->string('pagespeed_json', 255)->nullable()->after('pdf_file');
            $table->string('yslow_json', 255)->nullable()->after('pagespeed_json');
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
        Schema::table('store_views_gt_metrix', function (Blueprint $table) {
            $table->dropField('pagespeed_json');
            $table->dropField('yslow_json');
        });
    }
}
