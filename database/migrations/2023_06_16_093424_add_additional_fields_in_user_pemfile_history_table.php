<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsInUserPemfileHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->integer('server_id')->nullable()->after('user_id');
            $table->text('public_key')->nullable()->after('username');
            $table->string('access_type')->nullable()->after('public_key');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->dropColumn('public_key');
            $table->dropColumn('access_type');
        });
    }
}
