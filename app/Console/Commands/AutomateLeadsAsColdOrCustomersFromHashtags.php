<?php

namespace App\Console\Commands;

use App\Customer;
use App\Services\Instagram\Automation;
use Illuminate\Console\Command;

class AutomateLeadsAsColdOrCustomersFromHashtags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:leads-hashtags';

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
        $automation = new Automation();
        $customers = Customer::where('instahandler', '!=', '')->where('rating', '>', 5)->orderBy('created_at', 'DESC')->orderBy('rating', 'DESC')->paginate(5);
        $customers = $customers->toArray();
        $customerProfiles = $customers['data'];

        foreach ($customerProfiles as $customerProfile) {
            [$followers, $following] = $automation->getUserDetails($customerProfile['instahandler'], true);

            foreach ($followers as $follower) {
//                $lead = $automation->getUserDetails('farfetch');
                $lead = $automation->getUserDetails($follower['username']);
                $leadPercentage = $automation->getOverallLeadPercentage($lead);
                echo $follower['username'] . " => " .$leadPercentage[0] . "\n";
                if ($leadPercentage[0] > 40) {
                    dd($leadPercentage, $lead['info']);
                }
            }

        }
    }
}
