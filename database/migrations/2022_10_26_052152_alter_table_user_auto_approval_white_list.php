<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUserAutoApprovalWhiteList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `users` MODIFY COLUMN `is_auto_approval` INTEGER (1) NOT NULL DEFAULT "1";');
        DB::statement('ALTER TABLE `users` MODIFY COLUMN `is_whitelisted` INTEGER (1) NOT NULL DEFAULT "1";');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `users` MODIFY COLUMN `is_auto_approval` INTEGER (1) NOT NULL DEFAULT "0";');
        DB::statement('ALTER TABLE `users` MODIFY COLUMN `is_whitelisted` INTEGER (1) NOT NULL DEFAULT "0";');
    }
}
