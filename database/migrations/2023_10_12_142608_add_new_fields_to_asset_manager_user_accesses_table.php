<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToAssetManagerUserAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_manager_user_accesses', function (Blueprint $table) {
            $table->string('login_type')->after('usernamehost')->nullable();
            $table->string('key_type')->after('login_type')->nullable();
            $table->string('user_role')->after('key_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_manager_user_accesses', function (Blueprint $table) {
            //
        });
    }
}
