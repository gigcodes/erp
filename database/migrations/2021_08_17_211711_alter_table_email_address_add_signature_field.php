<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEmailAddressAddSignatureField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('email_addresses', function (Blueprint $table) {
            $table->string('signature_name')->nullable()->after('store_website_id');
            $table->string('signature_title')->nullable()->after('signature_name');
            $table->string('signature_phone')->nullable()->after('signature_title');
            $table->string('signature_email')->nullable()->after('signature_phone');
            $table->string('signature_website')->nullable()->after('signature_email');
            $table->text('signature_address')->nullable()->after('signature_website');
            $table->string('signature_logo')->nullable()->after('signature_address');
            $table->string('signature_image')->nullable()->after('signature_logo');
            $table->text('signature_social')->nullable()->after('signature_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('email_addresses', function (Blueprint $table) {
        });
    }
}
