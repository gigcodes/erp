<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubjectStaticTemplateToMailinglistTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailinglist_templates', function (Blueprint $table) {
            $table->String('subject',255)->nullable();
            $table->text('static_template')->nullable();
			$table->string('mail_tpl')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailinglist_templates', function (Blueprint $table) {
            $table->dropColumn('store_id');
            $table->dropColumn('static_template');
        });
    }
}
