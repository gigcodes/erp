<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoPagebuilderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_pagebuilder', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('magento_store_id');
            $table->timestamp('creation_time')->nullable();
            $table->timestamp('update_time')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreign('magento_store_id')
                ->references('id')
                ->on('store_websites')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_pagebuilder');
    }
}
