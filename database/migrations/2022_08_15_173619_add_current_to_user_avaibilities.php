<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentToUserAvaibilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_avaibilities', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `user_avaibilities` CHANGE `user_id` `user_id` BIGINT UNSIGNED NOT NULL;');
            \DB::statement('ALTER TABLE `user_avaibilities` ADD INDEX(`user_id`);');
            \DB::statement('ALTER TABLE `user_avaibilities` ADD INDEX(`status`);');
            \DB::statement('ALTER TABLE `user_avaibilities` ADD `is_latest` TINYINT NOT NULL AFTER `note`, ADD INDEX (`is_latest`); ');
            \DB::statement('ALTER TABLE `user_avaibilities` CHANGE `launch_time` `lunch_time` TIME NULL DEFAULT NULL;');

            // \DB::statement('ALTER TABLE `user_avaibilities` DROP `day`;');
            // \DB::statement('ALTER TABLE `user_avaibilities` DROP `minute`;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_avaibilities', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `user_avaibilities` CHANGE `lunch_time` `launch_time` TIME NULL DEFAULT NULL;');
            $table->dropColumn('is_latest');
        });
    }
}
