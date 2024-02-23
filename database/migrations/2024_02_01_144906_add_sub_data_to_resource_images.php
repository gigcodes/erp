<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubDataToResourceImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resource_images', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('url');
            $table->string('sender')->nullable()->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resource_images', function (Blueprint $table) {
            $table->dropColumn('subject');
            $table->dropColumn('sender');
        });
    }
}
