<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankStatementFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_statement_file', function (Blueprint $table) {
            $table->id();
            $table->text('filename');
            $table->text('path');
            $table->text('mapping_fields'); //json field with mapping of database
            $table->string('status')->nullable();
            $table->integer("created_by")->default(0);//logged in user id
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
        Schema::dropIfExists('bank_statement_file');
    }
}
