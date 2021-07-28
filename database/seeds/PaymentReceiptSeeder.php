<?php

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
        factory(PaymentReceipt::class, 10000)->create();
    }
}
