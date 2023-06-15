<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModuleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("magento_module_id")->nullable();
            $table->integer("store_website_id")->nullable();
            $table->integer("updated_by")->nullable();
            $table->string("command")->nullable();
            $table->string("job_id")->nullable();
            $table->string("status")->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_website_environment_histories');
    }
}
