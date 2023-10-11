<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetManagerUserAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_manager_user_accesses', function (Blueprint $table) {
            $table->id();
            $table->integer('assets_management_id');
            $table->integer('user_id');
            $table->string('username');
            $table->string('password');
            $table->longText('usernamehost');
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('asset_manager_user_accesses');
    }
}
