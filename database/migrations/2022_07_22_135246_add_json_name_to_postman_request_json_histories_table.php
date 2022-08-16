<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJsonNameToPostmanRequestJsonHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_request_json_histories', function (Blueprint $table) {
            $table->string('json_Name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postman_request_json_histories', function (Blueprint $table) {
            $table->dropColumn('json_Name');
        });
    }
}
