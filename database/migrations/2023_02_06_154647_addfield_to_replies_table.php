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
        
         // \DB::statement("ALTER TABLE `replies` ADD `platform_id` INT NULL;");
         // \DB::statement("ALTER TABLE `translate_Replies` ADD `platform_id` INT NULL;");

        Schema::table('replies', function ($table) {
            $table->string('platform_id')->nullable();
        });

        Schema::table('translate_Replies', function ($table) {
            $table->string('platform_id')->nullable();
        });
      
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