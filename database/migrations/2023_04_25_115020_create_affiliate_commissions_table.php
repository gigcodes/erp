<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('affiliate_commissions')) {
            Schema::create('affiliate_commissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_account_id');
                $table->string('affiliate_commission_id');
                $table->float('amount')->default(0);
                $table->boolean('approved');
                $table->dateTime('affiliate_commission_created_at');
                $table->string('commission_type');
                $table->float('conversion_sub_amount')->default(0);
                $table->text('comment')->nullable();
                $table->string('affiliate_conversion_id')->nullable();
                $table->string('payout')->nullable();
                $table->string('affiliate_marketer_id');
                $table->string('kind');
                $table->string('currency');
                $table->string('final')->nullable();
                $table->dateTime('finalization_date')->nullable();
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
        Schema::dropIfExists('affiliate_commissions');
    }
}
