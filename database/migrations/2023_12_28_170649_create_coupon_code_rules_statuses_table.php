<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodeRulesStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_code_rules_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('coupon_code_rules_statuses')->insert([
            'status_name' => 'Active',
            'status_alias' => 1,
        ]);

        \Illuminate\Support\Facades\DB::table('coupon_code_rules_statuses')->insert([
            'status_name' => 'InActive',
            'status_alias' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_code_rules_statuses');
    }
}
