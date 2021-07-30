<?php

use App\DeveloperTask;
use App\Task;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\PaymentReceipt::class, function (Faker $faker) {

            $task_ids = Task::all()->pluck('id');
            $user_ids = User::all()->pluck('id');
            $developer_task_ids =  DeveloperTask::all()->pluck('id');

            $billing_start_date = $faker->dateTimeBetween('-5 years','-2 years')->format('Y-m-d');


            $billing_end_date  =Carbon::createFromFormat('Y-m-d', $billing_start_date)->addDay(rand(1,200));

            $created_at = $faker->dateTimeBetween('-10 years','now');
            $datee =Carbon::now()->createFromFormat('Y-m-d', $billing_start_date); 
            // $datee =Carbon::now()->subDay(rand(-3,3))->format('Y-m-d'); 

            return [
                    
                    'worked_minutes'=>$faker->numberBetween(1,500),
                    'payment'=>$faker->randomFloat(2,1,1000),
                    'status'=>$faker->randomElement(['done','Pending']),
                    'task_id'=>$faker->randomElement($task_ids),
                    'developer_task_id'=>$faker->randomElement($developer_task_ids),
                    'rate_estimated'=>$faker->numberBetween(1,500),
                    'created_at'=>$created_at,
                    'updated_at'=>$created_at,
                    'remarks'=>$faker->sentence(5,true),
                    'date'=>$datee,
                    'currency'=>$faker->currencyCode(),
                    'user_id'=>$faker->randomElement($user_ids),
                    'billing_start_date'=>$billing_start_date,
                    'billing_end_date'=>$billing_end_date,
                    'billing_due_date'=>$faker->dateTimeBetween('-2 years','now'),
                    'by_command'=>$faker->numberBetween(0,3),

                ];
});
