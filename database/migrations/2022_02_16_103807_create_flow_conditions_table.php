<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flow_name');
            $table->string('condition_name');
            $table->string('message');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        DB::table('flow_conditions')->insert([
            [
                'flow_name' => 'wishlist',
                'condition_name' => 'wishlist_customer_basket_products_created_at',
                'message' => 'Wishlist date check',
            ], [
                'flow_name' => 'delivered_order',
                'condition_name' => 'delivered_order_orders_order_status',
                'message' => 'check if order delivered ',
            ], [
                'flow_name' => 'delivered_order',
                'condition_name' => 'delivered_order_orders_date_of_delivery',
                'message' => 'Order delivery date check ',
            ], [
                'flow_name' => 'newsletters',
                'condition_name' => 'newsletters_list_contacts_created_at',
                'message' => 'Newsletters created_at date check',
            ], [
                'flow_name' => 'newsletters',
                'condition_name' => 'newsletters_customers_newsletter',
                'message' => 'Check if subscribed for newsletter',
            ], [
                'flow_name' => 'customer_win_back',
                'condition_name' => 'customer_win_back_orders_order_status',
                'message' => 'In the customer_win_back for _orders , the order_status is exit ',
            ], [
                'flow_name' => 'customer_win_back',
                'condition_name' => 'customer_win_back_orders_created_at',
                'message' => 'Customer winback, order date check',
            ], [
                'flow_name' => 'order_reviews',
                'condition_name' => 'order_reviews_customers_store_website_id',
                'message' => 'Order_reviews customers store website check ',
            ], [
                'flow_name' => 'order_reviews',
                'condition_name' => 'order_reviews_orders_order_status',
                'message' => 'Order_reviews order_status check',
            ], [
                'flow_name' => 'order_reviews',
                'condition_name' => 'order_reviews_orders_date_of_delivery',
                'message' => 'Order_reviews delivery date check',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_yes_flow_paths_developer_tasks_created_at',
                'message' => 'Developer task date check',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_yes_flow_paths_scraper_id',
                'message' => 'check if task type is scrapper task for merged pr ',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_yes_flow_paths_is_pr_merged',
                'message' => 'Check if task pr merged ',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_no_flow_paths_developer_tasks_created_at',
                'message' => 'Developer task date check ',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_no_flow_paths_scraper_id',
                'message' => 'Scrapper id check if pr not merged ',
            ], [
                'flow_name' => 'task_pr',
                'condition_name' => 'check_if_pr_merged_no_flow_paths_is_pr_not_merged',
                'message' => ' check if pr not merged ',
            ],
        ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flow_conditions');
    }
}
