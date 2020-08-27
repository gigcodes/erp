<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDuplicateInstagramUserIdInInstagramThreadsTable extends Migration
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
            //
        });
    }
}
