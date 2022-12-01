<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableDocumentsBlobLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `documents` CHANGE `file_contents` `file_contents` LONGBLOB NULL DEFAULT NULL;');
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
