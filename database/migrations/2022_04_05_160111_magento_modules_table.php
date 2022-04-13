<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class MagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_category_id')->nullable();
            $table->index('module_category_id'); //Index column 
            $table->string('module', 255)->nullable();
            $table->text("module_description")->nullable();
            $table->string('current_version', 40)->nullable();
            $table->integer('task_status')->nullable();
            $table->text('last_message')->nullable();
            $table->string('cron_time', 45)->nullable();
            $table->text('module_type')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('is_sql')->nullable();
            $table->boolean('is_third_party_plugin')->nullable();
            $table->boolean('is_third_party_js')->nullable();
            $table->boolean('is_js_css')->nullable();
            $table->index('status'); //Index column 
            $table->enum('payment_status', ['Free', 'Paid'])->default('Free');
            $table->index('payment_status'); //Index column 
            $table->text('developer_name')->nullable();
            $table->boolean('is_customized')->default(0);
            $table->index('is_customized');	//Index column 
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
        //
    }
}
