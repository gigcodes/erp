<?php

namespace Database\Factories;

use App\Team;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $created_at = $this->faker->dateTimeBetween('-10 years', 'now');

        $user_ids = User::all()->pluck('id');
        $user_id = $this->faker->randomElement($user_ids);

        return [
            'name' => $this->faker->word(),
            'user_id' => $user_id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Team $team) {
            $user_ids = User::all()->pluck('id');
            $team_ids = Team::all()->pluck('id');
            DB::table('team_user')->insert([
                'user_id' => $this->faker->randomElement($user_ids),
                'team_id' => $this->faker->randomElement($team_ids),
            ]);
        });
    }
}
