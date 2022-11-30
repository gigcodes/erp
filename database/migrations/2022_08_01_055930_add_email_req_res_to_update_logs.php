<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailReqResToUpdateLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('update_logs', function (Blueprint $table) {
            $table->string('email')->nullable()->after('request_header');
            $table->longText('request_body')->nullable()->after('email');
            $table->string('response_code')->nullable()->after('request_body');
            $table->longText('response_body')->nullable()->after('response_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('update_logs', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('request_body');
            $table->dropColumn('response_code');
            $table->dropColumn('response_code');
        });
    }
}
