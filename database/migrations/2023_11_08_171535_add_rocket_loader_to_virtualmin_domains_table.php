<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRocketLoaderToVirtualminDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtualmin_domains', function (Blueprint $table) {
            $table->string('rocket_loader')->after('is_enabled')->nullable()->default('off');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('virtualmin_domains', function (Blueprint $table) {
            //
        });
    }
}
