<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsInStoreWebsiteEnvironmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_environments', function (Blueprint $table) {
            $table->text('path')->after('store_website_id')->nullable();
            $table->string('value')->after('path')->nullable();
            $table->string('command')->after('value')->nullable();
            $table->integer('created_by')->after('command')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_environments', function (Blueprint $table) {
            $table->dropColumn('path');
            $table->dropColumn('value');
            $table->dropColumn('command');
            $table->dropColumn('created_by');
        });
    }
}
