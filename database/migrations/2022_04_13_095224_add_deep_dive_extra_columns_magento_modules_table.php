<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeepDiveExtraColumnsMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->integer('task_status')->nullable();
            $table->text('last_message')->nullable();
            $table->string('cron_time', 45)->nullable();
            $table->boolean('is_sql')->nullable();
            $table->boolean('is_third_party_plugin')->nullable();
            $table->boolean('is_third_party_js')->nullable();
            $table->boolean('is_js_css')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('last_message');
            $table->dropColumn('cron_time', 45);
            $table->dropColumn('task_status');
            $table->dropColumn('is_sql');
            $table->dropColumn('is_third_party_plugin');
            $table->dropColumn('is_third_party_js');
            $table->dropColumn('is_js_css');
        });
    }
}
