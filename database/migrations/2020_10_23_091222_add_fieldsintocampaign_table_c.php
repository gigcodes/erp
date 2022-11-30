<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsintocampaignTableC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googlecampaigns', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->after('id')->nullable();
            $table->foreign('account_id')->references('id')->on('googleadsaccounts');
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
