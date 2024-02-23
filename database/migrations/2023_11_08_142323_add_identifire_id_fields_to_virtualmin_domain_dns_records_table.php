<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdentifireIdFieldsToVirtualminDomainDnsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtualmin_domain_dns_records', function (Blueprint $table) {
            $table->string('identifier_id')->after('Virtual_min_domain_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('virtualmin_domain_dns_records', function (Blueprint $table) {
            //
        });
    }
}
