<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteDevelopmentDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_development_documents', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('site_development_id')->nullable();
			$table->integer('site_development_category_id')->nullable();
			$table->integer('store_website_id')->nullable();
            $table->string('subject');
			$table->text('description', 65535)->nullable();
			$table->integer('created_by')->nullable();
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
        Schema::dropIfExists('site_development_documents');
    }
}