<?php

use Illuminate\Database\Migrations\Migration;

class UpdateMessageLimitsToUnitables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE `ui_check_communications` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
        \DB::statement('ALTER TABLE `uichecks` CHANGE `communication_message` `communication_message` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE `ui_check_communications` CHANGE `message` `message` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
        \DB::statement('ALTER TABLE `uichecks` CHANGE `communication_message` `communication_message` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
    }
}
