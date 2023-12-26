<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductListingFinalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_listing_final_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        $statusArray = ['Category Incorrect', 'Price Not Correct', 'Price Not Found', 'Color Not Found', 'Category Not Found', 'Description Not Found', 'Details Not Found', 'Composition Not Found', 'Crop Rejected', 'Other'];

        foreach ($statusArray as $key => $value) {
            \Illuminate\Support\Facades\DB::table('product_listing_final_statuses')->insert([
                'status_name' => $value,
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_listing_final_statuses');
    }
}
