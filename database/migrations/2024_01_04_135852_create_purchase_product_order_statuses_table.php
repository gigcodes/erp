<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseProductOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_product_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('purchase_product_order_statuses')->insert([
            'status_name'  => 'Pending',
            'status_alias' => 'pending',
        ]);

        \Illuminate\Support\Facades\DB::table('purchase_product_order_statuses')->insert([
            'status_name'  => 'Complete',
            'status_alias' => 'complete',
        ]);

        \Illuminate\Support\Facades\DB::table('purchase_product_order_statuses')->insert([
            'status_name'  => 'In Stock',
            'status_alias' => 'in_stock',
        ]);

        \Illuminate\Support\Facades\DB::table('purchase_product_order_statuses')->insert([
            'status_name'  => 'Out Stock',
            'status_alias' => 'out_stock',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_product_order_statuses');
    }
}
