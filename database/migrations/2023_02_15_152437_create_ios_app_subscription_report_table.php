<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppSubscriptionReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    
        Schema::create('ios_subscription_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('group_by');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
              $table->integer('active_subscriptions');
        $table->integer('active_free_trials');
        $table->integer('new_subscriptions');
        $table->integer('cancelled_subscriptions');
        $table->integer('new_trials');
        $table->string('trial_conversion_rate');
        $table->string('mrr');
        $table->string('actual_revenue');
        $table->integer('renewals');
        $table->integer('first_year_subscribers');
        $table->integer('non_first_year_subscribers');
        $table->integer('reactivations');
        $table->integer('transitions_out');
        $table->integer('trial_cancellations');
        $table->integer('transitions_in');
        $table->integer('activations');
        $table->integer('cancellations');
        $table->integer('trial_conversions');
        $table->string('churn');
        $table->string('gross_revenue');
        $table->string('gross_mrr');
        $table->integer('active_grace');
        $table->integer('new_grace');
        $table->integer('grace_drop_off');
        $table->integer('grace_recovery');
        $table->integer('new_trial_grace');
        $table->integer('trial_grace_drop_off');
        $table->integer('trial_grace_recovery');
        $table->integer('active_trials');
        $table->integer('active_discounted_subscriptions');
        $table->integer('all_active_subscriptions');
        $table->integer('paying_subscriptions');
        $table->integer('all_subscribers');
        $table->string('storefront');
        $table->string('store');
      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ios_subscription_report');
    }
}
