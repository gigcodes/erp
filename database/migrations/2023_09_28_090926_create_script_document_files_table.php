<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScriptDocumentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_document_files', function (Blueprint $table) {
            $table->id();
            $table->integer('script_document_id')->default(0);
            $table->text('file_name')->nullable();
            $table->string('extension')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('google_drive_file_id')->nullable();
            $table->string('file_creation_date')->nullable();
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
        Schema::dropIfExists('script_document_files');
    }
}
