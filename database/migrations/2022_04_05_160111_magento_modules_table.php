<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->text('module_description')->nullable();
            $table->string('current_version', 40)->nullable();
            $table->text('module_type')->nullable();
            $table->boolean('status')->nullable();
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
