<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
         DB::statement("ALTER TABLE `replies` ADD `platform_id` INT NULL AFTER `is_pushed`;");
         DB::statement("ALTER TABLE `translate_Replies` ADD `platform_id` INT NULL AFTER `translate_text`;");
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};