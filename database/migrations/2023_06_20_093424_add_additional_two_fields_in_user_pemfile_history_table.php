<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalTwoFieldsInUserPemfileHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->string('server_ip')->nullable()->after('server_name');
            $table->string('user_role')->nullable()->after('access_type');
            $table->text('pem_content')->nullable()->after('user_role');
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
            $table->dropColumn('server_ip');
            $table->dropColumn('user_role');
            $table->dropColumn('pem_content');
        });
    }
}
