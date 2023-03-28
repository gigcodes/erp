<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPriorityTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_priority', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_priority');
    }
}