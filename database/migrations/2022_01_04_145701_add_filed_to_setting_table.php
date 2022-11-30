<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFiledToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("INSERT INTO `settings` (`id`, `name`, `val`, `type`, `created_at`, `updated_at`, `welcome_message`) VALUES (NULL, 'run_mailing_command', '1', 'int', NULL, NULL, NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
