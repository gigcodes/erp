<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('old', function (Blueprint $table) {
            DB::statement('ALTER TABLE `old` CHANGE `status` `status` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;');
            DB::statement('ALTER TABLE `old` CHANGE `category_id` `category_id` INT(11) NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('old', function (Blueprint $table) {
            //
        });
    }
}
