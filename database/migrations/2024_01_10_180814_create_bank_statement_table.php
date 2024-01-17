<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankStatementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_statement', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_statement_file_id')->default(0); //bank_statement_file table id
            $table->date('transaction_date');
            $table->text('transaction_reference_no');
            $table->string('debit_amount');
            $table->string('credit_amount');
            $table->string('balance');
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
        Schema::dropIfExists('bank_statement');
    }
}
