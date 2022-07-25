<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBodyJsonIdToPostmanRequestCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->integer('json_body_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->dropColumn('json_body_id');
        });
    }
}
