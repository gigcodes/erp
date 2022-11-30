<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMarketingTemplateCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('mailinglist_templates', function (Blueprint $table) {
            $table->text('salutation')->nullable()->after('mail_tpl');
            $table->text('introduction')->nullable()->after('salutation');
            $table->string('logo')->nullable()->after('introduction');
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
