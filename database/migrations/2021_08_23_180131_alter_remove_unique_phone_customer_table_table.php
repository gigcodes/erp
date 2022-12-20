<?php

use Illuminate\Database\Migrations\Migration;

class AlterRemoveUniquePhoneCustomerTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        try {
            DB::select('ALTER TABLE `customers` DROP INDEX `phone`;');
            DB::select('ALTER TABLE `customers` ADD INDEX(`phone`);');
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
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
