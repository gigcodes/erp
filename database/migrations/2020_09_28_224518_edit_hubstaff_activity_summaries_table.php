<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditHubstaffActivitySummariesTable extends Migration
{

    public function __construct()
    {
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('json', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            $table->text('rejection_note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            //
        });
    }
}
