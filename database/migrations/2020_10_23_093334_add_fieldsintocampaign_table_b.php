<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsintocampaignTableB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googlecampaigns', function (Blueprint $table) {
            $table->string('budget_uniq_id')->after('end_date')->nullable();
            $table->string('budget_id')->after('budget_uniq_id')->nullable();
            $table->text('campaign_response')->after('budget_id')->nullable();
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
            //
        });
    }
}
