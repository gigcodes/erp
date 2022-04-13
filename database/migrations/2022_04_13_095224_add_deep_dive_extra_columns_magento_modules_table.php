<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->text('last_message')->nullable();
            $table->string('cron_time', 45)->nullable();
            $table->integer('task_status')->nullable();
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
            //
        });
    }
}
