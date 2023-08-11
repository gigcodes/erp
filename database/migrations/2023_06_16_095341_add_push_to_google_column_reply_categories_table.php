<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPushToGoogleColumnReplyCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reply_categories', function (Blueprint $table) {
            $table->boolean('push_to_google')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reply_categories', function (Blueprint $table) {
            $table->dropColumn('push_to_google');
        });
    }
}
