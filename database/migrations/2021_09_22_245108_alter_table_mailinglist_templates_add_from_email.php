<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMailinglistTemplatesAddFromEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('mailinglist_templates',function(Blueprint $table) {
            $table->string("from_email")->nullable()->after("subject");
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
        Schema::table('mailinglist_templates',function(Blueprint $table) {
            $table->dropField("from_email");
        });
    }
}
