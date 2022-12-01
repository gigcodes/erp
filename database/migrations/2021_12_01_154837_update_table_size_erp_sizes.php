<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableSizeErpSizes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('size_erp_sizes', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `size_erp_sizes` CHANGE `system_size_id` `system_size_id` INT(11) NULL;');
            \DB::statement('ALTER TABLE `size_erp_sizes` CHANGE `erp_size_id` `erp_size_id` INT(11) NULL;');
        });
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
