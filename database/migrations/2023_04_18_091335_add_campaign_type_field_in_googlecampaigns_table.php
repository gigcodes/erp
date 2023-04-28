<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignTypeFieldInGooglecampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googlecampaigns', function (Blueprint $table) {
            $table->string('type')->default('ads')->after('app_store');
            $table->bigInteger('google_customer_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('googlecampaigns', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
