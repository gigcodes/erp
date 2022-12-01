<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModuleHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('magento_module_id');
            $table->integer('module_category_id')->nullable();
            // $table->index('module_category_id'); //Index column
            $table->string('module', 255)->nullable();
            $table->text('module_description')->nullable();
            $table->string('current_version', 40)->nullable();
            $table->text('module_type')->nullable();
            $table->boolean('status')->nullable();
            // $table->index('status'); //Index column
            $table->enum('payment_status', ['Free', 'Paid'])->default('Free');
            // $table->index('payment_status'); //Index column
            $table->text('developer_name')->nullable();
            $table->boolean('is_customized')->default(0);
            // $table->index('is_customized');	//Index column
            $table->text('last_message')->nullable();
            $table->integer('task_status')->nullable();
            $table->boolean('is_sql')->nullable();
            $table->boolean('is_third_party_plugin')->nullable();
            $table->boolean('is_third_party_js')->nullable();
            $table->boolean('is_js_css')->nullable();
            $table->boolean('store_website_id')->nullable();
            $table->boolean('api')->nullable();
            $table->boolean('cron_job')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('magento_module_histories');
    }
}
