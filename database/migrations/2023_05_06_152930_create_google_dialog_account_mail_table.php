<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDialogAccountMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_dialog_account_mails', function (Blueprint $table) {
            $table->id();
            $table->string('google_account')->nullable();
            $table->string('google_dialog_account_id')->nullable();
            $table->string('google_client_refresh_token')->nullable();
            $table->string('google_client_access_token')->nullable();
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
        Schema::dropIfExists('google_dialog_account_mails');
    }
}
