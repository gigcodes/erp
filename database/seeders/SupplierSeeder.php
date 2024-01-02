<?php

namespace Database\Seeders;

use App\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        // Create 1000 suppliers
        for ($i = 0; $i < 5000; $i++) {
            try {
                $supplier = new Supplier();
                $supplier->id = Supplier::count() + 100000;
                $supplier->supplier = $faker->name;
                $supplier->email = $faker->email;
                $supplier->address = $faker->address;
                $supplier->phone = $faker->phoneNumber;
                $supplier->default_phone = $faker->phoneNumber;
                $supplier->whatsapp_number = $faker->phoneNumber;
                $supplier->save();
            } catch(\Exception $e) {
            }
        }
    }
}
