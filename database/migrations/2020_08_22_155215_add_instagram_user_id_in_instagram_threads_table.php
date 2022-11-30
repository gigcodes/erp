<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstagramUserIdInInstagramThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instagram_threads', function (Blueprint $table) {
            $table->integer('instagram_user_id')->after('thread_v2_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instagram_threads', function (Blueprint $table) {
            $table->dropColumn('instagram_user_id');
        });
    }
}
