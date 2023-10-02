<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('file')->nullable();
            $table->string('category')->nullable();
            $table->string('usage_parameter')->nullable();
            $table->text('comments')->nullable();
            $table->string('author')->nullable();
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
        Schema::dropIfExists('script_documents');
    }
}
