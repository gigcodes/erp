<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualminDomainDnsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualmin_domain_dns_records', function (Blueprint $table) {
            $table->id();
            $table->integer('Virtual_min_domain_id');
            $table->string('dns_type', 255)->nullable();
            $table->string('content', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('domain_with_dns_name', 255)->nullable();
            $table->string('proxied', 255)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('virtualmin_domain_dns_records');
    }
}
