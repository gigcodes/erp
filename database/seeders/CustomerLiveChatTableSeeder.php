<?php

namespace Database\Seeders;

use App\Customer;
use App\CustomerLiveChat;
use Illuminate\Database\Seeder;

class CustomerLiveChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customer::inRandomOrder()->limit(6000)->get();

        // Load Faker
        $faker = \Faker\Factory::create();

        foreach ($customers as $customer) {
            $customerLiveChat = new CustomerLiveChat();
            $customerLiveChat->customer_id = $customer->id;
            $customerLiveChat->thread = $faker->name;
            $customerLiveChat->save();
        }
    }
}
