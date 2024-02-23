<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirtualminDomainDnsRecordsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualmin_domain_dns_records_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('Virtual_min_domain_id');
            $table->integer('user_id');
            $table->text('command');
            $table->text('output');
            $table->text('status');
            $table->text('error')->nullable();
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
        Schema::dropIfExists('virtualmin_domain_dns_records_histories');
    }
}
