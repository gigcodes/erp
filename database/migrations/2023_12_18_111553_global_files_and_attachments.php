<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GlobalFilesAndAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_files_and_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->string('module')->nullable();
            $table->string('title')->nullable();
            $table->string('filename')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('global_files_and_attachments');
    }
}
