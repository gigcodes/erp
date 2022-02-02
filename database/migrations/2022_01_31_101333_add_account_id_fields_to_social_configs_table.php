<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountIdFieldsToSocialConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable();
            $table->text('token')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_configs', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
    }
}
