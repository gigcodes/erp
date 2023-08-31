<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesColumnInVirtualminDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtualmin_domains', function (Blueprint $table) {
            $table->timestamp('start_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
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
            $table->dropColumn('start_date');
            $table->dropColumn('expiry_date');
        });
    }
}
