<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVarnishStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('varnish_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by');
            $table->integer('store_website_id')->nullable();
            $table->integer('assets_manager_id')->nullable();
            $table->string('server_name')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('website_name')->nullable();
            $table->string('cache_name')->nullable();
            $table->integer('cache_hit');
            $table->integer('cache_miss');
            $table->integer('cache_hitpass');
            $table->float('cache_hitrate', 8,2);
            $table->float('cache_missrate', 8,2);
            $table->longText('request_data');
            $table->longText('response_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('varnish_stats');
    }
}
