<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestResponseToStoreWebsiteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_users', function (Blueprint $table) {
            $table->longText('request_data')->after('user_role')->nullable();
            $table->longText('response_data')->after('request_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_users', function (Blueprint $table) {
            //
        });
    }
}
