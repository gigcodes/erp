<?php

namespace Database\Seeders;

use App\UicheckType;
use Illuminate\Database\Seeder;

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
