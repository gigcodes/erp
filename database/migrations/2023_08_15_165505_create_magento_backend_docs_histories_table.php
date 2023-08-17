<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoBackendDocsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_backend_docs_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_backend_docs_id');
            $table->text('column_name');  
            $table->integer("old_id")->nullable();
            $table->integer("new_id")->nullable();
            $table->text("old_value")->nullable();
            $table->text("new_value")->nullable();
            $table->text("google_drive_file_id")->nullable();
            $table->integer("user_id");
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
        Schema::dropIfExists('magento_backend_docs_histories');
    }
}
