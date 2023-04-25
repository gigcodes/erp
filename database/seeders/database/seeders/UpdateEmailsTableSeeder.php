<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Email;

class UpdateEmailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $emails = Email::all();

        foreach($emails as $email){
            $data = explode('@',$email->from);
            $name = $data[0];
            $email->name = $name;
            $email->save();
        }
    }
}