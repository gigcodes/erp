<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSeoProcessTableCloumnChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seo_process', function (Blueprint $table) {
            $table->bigInteger('seo_status_id')->nullable()->after('is_price_approved');
            $table->bigInteger('publish_status_id')->nullable()->after('seo_status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seo_process', function (Blueprint $table) {
            $table->dropColumn(['seo_status_id', 'publish_status_id']);
        });
    }
}
