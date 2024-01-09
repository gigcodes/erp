<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapStatisticsStausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrap_statistics_stauses', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('status_value')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'N/A',
            'status_value' => '',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Ok',
            'status_value' => 'Ok',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Rework',
            'status_value' => 'Rework',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'In Process',
            'status_value' => 'In Process',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Scrapper Fixed',
            'status_value' => 'Scrapper Fixed',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Process Complete',
            'status_value' => 'Process Complete',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Categories',
            'status_value' => 'Categories',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Logs Checked',
            'status_value' => 'Logs Checked',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'Scrapper Checked',
            'status_value' => 'Scrapper Checked',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'All brands Scrapped',
            'status_value' => 'All brands Scrapped',
        ]);

        \Illuminate\Support\Facades\DB::table('scrap_statistics_stauses')->insert([
            'status' => 'All Categories Scrapped',
            'status_value' => 'All Categories Scrapped',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scrap_statistics_stauses');
    }
}
