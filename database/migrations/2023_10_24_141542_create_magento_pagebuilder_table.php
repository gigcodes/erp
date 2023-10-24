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
            $table->string('content_heading')->nullable();
            $table->string('page_layout')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('identifier')->nullable();
            $table->mediumText('content')->nullable();
            $table->timestamp('creation_time')->nullable();
            $table->timestamp('update_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->nullable();
            $table->text('custom_layout_update_xml')->nullable();
            $table->text('layout_update_xml')->nullable();
            $table->string('layout_update_selected', 128)->nullable();
            $table->string('custom_root_template')->nullable();
            $table->string('meta_title')->nullable();
            $table->date('custom_theme_from')->nullable();
            $table->date('custom_theme_to')->nullable();
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
