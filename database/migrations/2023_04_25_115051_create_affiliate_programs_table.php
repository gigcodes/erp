<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('affiliate_programs')) {
            Schema::create('affiliate_programs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_account_id');
                $table->string('affiliate_program_id');
                $table->string('currency');
                $table->string('title');
                $table->integer('cookie_time');
                $table->string('default_landing_page_url', 512);
                $table->boolean('recurring');
                $table->string('recurring_cap')->nullable();
                $table->string('recurring_period_days')->nullable();
                $table->string('program_category_id')->nullable();
                $table->string('program_category_identifier')->nullable();
                $table->string('program_category_title')->nullable();
                $table->boolean('program_category_is_admitad_suitable');
                $table->foreign('affiliate_account_id')->references('id')->on('affiliate_provider_accounts')->cascadeOnDelete()->cascadeOnUpdate();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_programs');
    }
}
