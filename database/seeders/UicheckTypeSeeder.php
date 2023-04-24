<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\UicheckType;

class UicheckTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Adding master entries for uicheck_types table');
        $rows = [
            ['name' => 'UI Test'],
            ['name' => 'UI Design'],
        ];

        UicheckType::insert($rows);
        $this->command->info('Master entry added successfully');
    }
}
