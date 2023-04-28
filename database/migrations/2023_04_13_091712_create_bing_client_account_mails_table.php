<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBingClientAccountMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bing_client_account_mails', function (Blueprint $table) {
            $table->id();
            $table->string('bing_account', 191);
            $table->unsignedInteger('bing_client_account_id');
            $table->text('bing_client_refresh_token');
            $table->text('bing_client_access_token');
            $table->integer('expires_in');
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
        Schema::dropIfExists('bing_client_account_mails');
    }
}
