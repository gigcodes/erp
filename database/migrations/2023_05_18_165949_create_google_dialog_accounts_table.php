<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDialogAccountsTable extends Migration
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
            $table->string('project_id')->nullable();
            $table->integer('site_id')->nullable();
            $table->string('service_file')->nullable();
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
        Schema::dropIfExists('google_dialog_accounts');
    }
}
