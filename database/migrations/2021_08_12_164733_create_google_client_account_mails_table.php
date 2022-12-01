<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGoogleClientAccountMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `google_client_accounts` DROP `GOOGLE_CLIENT_ACCESS_TOKEN`;');
        DB::select('ALTER TABLE `google_client_accounts` DROP `GOOGLE_CLIENT_REFRESH_TOKEN`;');
        Schema::create('google_client_account_mails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('google_account')->nullable();
            $table->string('google_client_account_id')->nullable();
            $table->string('GOOGLE_CLIENT_REFRESH_TOKEN')->nullable();
            $table->string('GOOGLE_CLIENT_ACCESS_TOKEN')->nullable();
            $table->integer('expires_in')->nullable();
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
        Schema::dropIfExists('google_client_account_mails');
    }
}
