<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ColdLeads;
use App\Customer;

class MoveColdLeadsToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold-leads:move-to-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move cold leads to the customer databas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get cold leads
        $coldLeads = ColdLeads::where('is_imported', 1)->where('customer_id', null)->inRandomOrder()->get();

        // Set count to 0 and maxcount to 50
        $count = 0;
        $maxCount = 50;

        // Loop over coldLeads
        if ($coldLeads !== null) {
            foreach ($coldLeads as $coldLead) {
                // Reached maxCount
                if ($count >= $maxCount) {
                    return;
                }

                // Add cold lead to customers table
                if (!$coldLead->customer && !$coldLead->whatsapp) {
                    // Create new customer
                    $customer = new Customer();
                    $customer->name = $coldLead->name;
                    $customer->phone = $coldLead->platform_id;
                    $customer->whatsapp_number = '919152731486';
                    $customer->city = $coldLead->address;
                    $customer->country = 'IN';
                    $customer->save();

                    if ( !empty($customer->id) ) {
                        $coldLead->customer_id = $customer->id;
                        $coldLead->save();
                    }

                    $count++;
                }

            }
        }
    }
}
