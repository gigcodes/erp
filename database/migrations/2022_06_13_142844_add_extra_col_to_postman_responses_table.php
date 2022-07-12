<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraColToPostmanResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_responses', function (Blueprint $table) {
            $table->string('response_code')->nullable()->after('request_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postman_responses', function (Blueprint $table) {
            $table->dropColumn('response_code');
        });
    }
}
