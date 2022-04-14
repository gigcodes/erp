<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMethodNameMessageLogRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_requests', function (Blueprint $table) {
            
            $table->string("message")->nullable();

            $table->string("method_name")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_requests', function (Blueprint $table) {
            
            $table->dropColumn("message");

            $table->dropColumn("method_name");
        });
    }
}
