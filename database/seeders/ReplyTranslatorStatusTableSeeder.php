<?php

namespace Database\Seeders;

use App\ReplyTranslatorStatus;
use Illuminate\Database\Seeder;

class ReplyTranslatorStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lists = ['new', 'approved', 'rejected'];

        foreach ($lists as $list) {
            ReplyTranslatorStatus::firstOrCreate([
                'name' => $list,
            ]);
        }
    }
}
