<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\Facebook\FB;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use Illuminate\Console\Command;

class SyncFacebookCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all facebook campaign linked with the added ad accounts';

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
     * @return int
     */
    public function handle()
    {
        $ad_accounts = SocialAdAccount::where('status', 1)->get();
        foreach ($ad_accounts as $account) {
            $fb = new FB($account->page_token);
            $results = $fb->getCampaigns('act_' . $account->ad_account_id);
            foreach ($results['campaigns'] as $campaign) {
                SocialCampaign::updateOrCreate(['ref_campaign_id' => $campaign['id']], [
                    'config_id' => $account->id,
                    'name' => $campaign['name'],
                    'objective_name' => $campaign['objective'],
                    'buying_type' => $campaign['buying_type'],
                    'daily_budget' => $campaign['daily_budget'] ?? null,
                    'live_status' => $campaign['status'],
                    'created_at' => Carbon::parse($campaign['created_time']),
                ]);
            }
        }
    }
}
