<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCompareFlagsToScraperImagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraper_imags', function (Blueprint $table) {
            $table->integer('compare_flag')->default(0);
            $table->integer('manually_approve_flag')->default(0);
        });

        //DB::table('scraper_imags')->update(['compare_flag' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraper_imags', function (Blueprint $table) {
            //
        });
    }
}
