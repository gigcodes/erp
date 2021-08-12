<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReadStatusToScrapInfluencersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::select("ALTER TABLE `scrap_influencers` ADD `read_status` INT(11) NULL AFTER `platform`;");
       DB::select("ALTER TABLE `scrap_influencers` ALTER COLUMN `read_status` SET DEFAULT '0';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scrap_influencers', function (Blueprint $table) {
            //
        });
    }
}
