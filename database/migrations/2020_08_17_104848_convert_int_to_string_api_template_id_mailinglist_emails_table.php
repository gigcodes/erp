<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertIntToStringApiTemplateIdMailinglistEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('mailinglist_emails', function (Blueprint $table) {
            $table->string('api_template_id')->change();
        });
        */
        \DB::statement('ALTER TABLE `mailinglist_emails` CHANGE `api_template_id` `api_template_id` VARCHAR(255) NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('mailinglist_emails', function (Blueprint $table) {
            $table->int('api_template_id')->change();
        });*/
    }
}
