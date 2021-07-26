<?php

use Faker\Generator as Faker;
use App\Task;
use App\Team;
use App\User;
use Illuminate\Support\Facades\DB;

$factory->define(App\Team::class, function (Faker $faker) {

    $created_at = $faker->dateTimeBetween('-10 years','now');

    $task_ids = Task::all()->pluck('id');
    $user_ids = User::all()->pluck('id');
    $team_ids = Team::all()->pluck('id');
    $user_id = $faker->randomElement($user_ids);

    DB::table('team_user')->insert([
        'user_id'=>$user_id,
        'team_id'=>$faker->randomElement($team_ids)
    ]);
    return [
        'name'=>$faker->word(),
        'user_id'=>$user_id,
        'created_at'=>$created_at,
        'updated_at'=>$created_at,
    ];




});
