<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCharityToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("INSERT INTO `brands` (`id`, `name`, `euro_to_inr`, `min_sale_price`, `max_sale_price`, `deduction_percentage`, `flash_sales_percentage`, `apply_b2b_discount_above`, `b2b_sales_discount`, `sales_discount`, `magento_id`, `brand_segment`, `sku_strip_last`, `sku_add`, `references`, `sku_search_url`, `google_server_id`, `priority`, `next_step`, `created_at`, `updated_at`, `deleted_at`, `brand_image`) VALUES (NULL, 'charity', '100', '10', '10', '10', '0', '0', '0', '0', '0', '2', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL);");
        DB::select("INSERT INTO `categories` (`id`, `parent_id`, `category_segment_id`, `title`, `magento_id`, `show_all_id`, `dimension_range`, `size_range`, `simplyduty_code`, `status_after_autocrop`, `created_at`, `updated_at`, `references`, `ignore_category`, `need_to_check_measurement`, `need_to_check_size`, `size_chart_needed`, `push_type`) VALUES (NULL, '1',  '1', 'charity', '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
