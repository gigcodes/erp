<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('assets_management_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('created_by')->default(0);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->longText('usernamehost')->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
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
