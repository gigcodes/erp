<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDialogAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_dialog_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('google_client_id')->nullable();
            $table->string('google_client_secret')->nullable();
            $table->integer('site_id')->nullable();
            $table->foreign('site_id')->references('id')->on('store_websites')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('google_dialog_account');
    }
}
