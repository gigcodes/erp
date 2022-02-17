<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->integer('flow_id');
            $table->string('condition_name');
			$table->string('message');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        DB::table('flow_conditions')->insert([
                    [
                        'flow_id' => '36',
                        'condition_name' => 'wishlist_store_website_id',
                        'message'=>'In the wishlist, the store website id is exit.'
                    ],[
                        'flow_id' => '36',
                        'condition_name' => 'wishlist_customer_basket_products_created_at',
                        'message'=>'In the wishlist for customer_basket_products , the created_at is exit'
                    ],[
                        'flow_id' => '18',
                        'condition_name' => 'delivered_order_customers_store_website_id',
                        'message'=>'In the delivered_order for customers , the store_website_id is exit '
                    ],[
                        'flow_id' => '18',
                        'condition_name' => 'delivered_order_orders_order_status',
                        'message'=>'In the delivered_order for customers , the order_status is exit '
                    ],[
                        'flow_id' => '18',
                        'condition_name' => 'delivered_order_orders_date_of_delivery',
                        'message'=>'In the delivered_order for order , the date_of_delivery is exit '
                    ],[
                        'flow_id' => '23',
                        'condition_name' => 'newsletters_mailinglists_website_id',
                        'message'=>'In the newsletters for mailinglists , the website_id is exit'
                    ],[
                        'flow_id' => '23',
                        'condition_name' => 'newsletters_list_contacts_created_at',
                        'message'=>'In the newsletters for list_contacts , the created_at is exit'
                    ],[
                        'flow_id' => '23',
                        'condition_name' => 'newsletters_customers_newsletter',
                        'message'=>'In the newsletters for customers , the newsletter is exit'
                    ],[
                        'flow_id' => '26',
                        'condition_name' => 'customer_win_back_customers_newsletter',
                        'message'=>'In the customer_win_back for customers , the newsletter is exit '
                    ],[
                        'flow_id' => '26',
                        'condition_name' => 'customer_win_back_orders_order_status',
                        'message'=>'In the customer_win_back for _orders , the order_status is exit '
                    ],[
                        'flow_id' => '26',
                        'condition_name' => 'customer_win_back_orders_created_at',
                        'message'=>'In the customer_win_back for _orders , the created_at is exit'
                    ],[
                        'flow_id' => '35',
                        'condition_name' => 'order_reviews_customers_store_website_id',
                        'message'=>'In the order_reviews for customers , the website_id is exit '
                    ],[
                        'flow_id' => '35',
                        'condition_name' => 'order_reviews_orders_order_status',
                        'message'=>'In the order_reviews for orders , the order_status is exit  '
                    ],[
                        'flow_id' => '35',
                        'condition_name' => 'order_reviews_orders_date_of_delivery',
                        'message'=>'In the order_reviews for orders , the date_of_delivery is exit  '
                    ],[
                        'flow_id' => '35',
                        'condition_name' => 'order_reviews_orders_date_of_delivery',
                        'message'=>'In the order_reviews for orders , the date_of_delivery is exit  '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_flow_paths_parent_action_id',
                        'message'=>'In the check_if_pr_merged_flow for flow_path , the parent_action_id is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_yes_flow_paths_developer_tasks_created_at',
                        'message'=>'In the check_if_pr_merged_flow for flow_path yes, the developer_tasks created_at is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_yes_flow_paths_scraper_id',
                        'message'=>'In the check_if_pr_merged_flow for flow_path yes, the scraper_id is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_yes_flow_paths_is_pr_merged_1',
                        'message'=>'In the check_if_pr_merged_flow for flow_path yes, the pr_merged 1 is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_no_flow_paths_developer_tasks_created_at',
                        'message'=>'In the check_if_pr_merged_flow for flow_path no, the developer_tasks_created_at is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_no_flow_paths_scraper_id',
                        'message'=>'In the check_if_pr_merged_flow for flow_path no, the scraper_id is exit '
                    ],[
                        'flow_id' => '33',
                        'condition_name' => 'check_if_pr_merged_no_flow_paths_is_pr_merged_0',
                        'message'=>'In the check_if_pr_merged_flow for flow_path no, the pr_merged_0 is exit '
                    ]]
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
