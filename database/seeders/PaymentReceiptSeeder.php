<?php

namespace Database\Seeders;

use App\PaymentReceipt;
use Illuminate\Database\Seeder;

class PaymentReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentReceipt::factory()->count(10000)->create();
    }
}
