<?php

namespace App\Console\Commands;

use App\CompetitorFollowers;
use App\CompetitorPage;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

class GetCompetitorFollowers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compig:get-followers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $comps = CompetitorPage::all();
        $instagram = new Instagram();
        $instagram->login('rishabh_aryal', 'R1shabh@123');

        foreach ($comps as $comp) {
            try {
                $profileData = $instagram->people->getInfoByName($comp->username)->asArray();
            } catch (\Exception $exception) {
                $profileData = [];
            }

            if (!isset($profileData['user'])) {
                return [];
            }


            $profileData = $profileData['user'];
            $rank = Signatures::generateUUID();
            $lastId = $comp->cursor ?? '';

            do {
                $followersAll = $instagram->people->getFollowers($profileData['pk'], $rank, '', $lastId)->asArray();
                $followers = $followersAll['users'];
                $lastId = $followersAll['next_max_id'];

                foreach ($followers as $follower)
                {

                    $u = CompetitorPage::where('username', $follower['username'])->first();
                    if ($u) {
                        continue;
                    }

                    $u = new CompetitorFollowers();
                    $u->competitor_id = $comp->id;
                    $u->username = $follower['username'];
                    $u->status = 1;
                    $u->save();
                }

                $comp->cursor = $lastId;
                $comp->save();

            } while($lastId != 'END');
        }

    }
}
