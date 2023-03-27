<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToGoogleAnalyticDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_analytic_datas', function (Blueprint $table) {
            $table->string('device')->nullable()->after('gender');
            $table->string('log')->nullable()->after('gender');
            $table->string('exception')->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_analytic_datas', function (Blueprint $table) {
            //
        });
    }
}
