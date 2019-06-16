<?php

namespace App\Console\Commands;

use App\ColdLeads;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class FilterColdLeadByPostCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:cold-leads';

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
        $coldLeads = ColdLeads::orderBy('id', 'DESC')->get();

        $instagram = new Instagram();
        $instagram->login('sololuxury.official', "NcG}4u'z;Fm7");

        foreach ($coldLeads as $coldLead) {
            $username = $coldLead->username;

            try {
                $coldLeadInstagram = $instagram->people->getInfoByName($username)->asArray();
            } catch (\Exception $exception) {
                continue;
            }

            echo "$username \n";

            $user = $coldLeadInstagram['user'];

            if ($user['media_count'] < 20) {
                try {
                    echo "DELETE \n";
                    $coldLead->delete();
                } catch (\Exception $exception) {
                }
            }
        }

    }
}
