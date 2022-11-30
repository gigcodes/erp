<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStoreViewGtMatrixPdf extends Migration
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
            $table->string('pdf_file')->nullable()->after('resources');
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
            $table->dropField('pdf_file');
        });
    }
}
