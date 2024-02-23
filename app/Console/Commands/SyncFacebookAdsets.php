<?php

namespace App\Console\Commands;

use App\Models\SocialAdAccount;
use App\Services\Facebook\FB;
use App\Social\SocialAdset;
use App\Social\SocialCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncFacebookAdsets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-adsets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all facebook adsets linked with the added ad accounts';

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
            $results = $fb->getAdsets($account->ad_account_id);
            foreach ($results['adsets'] as $adset) {
                SocialAdset::updateOrCreate(['ref_adset_id' => $adset['id']], [
                    'config_id' => $account->id,
                    'name' => $adset['name'],
                    'campaign_id' => $adset['campaign_id'],
                    'destination_type' => $adset['destination_type'],
                    'billing_event' => $adset['billing_event'],
                    'start_time' => Carbon::parse($adset['start_time']),
                    'end_time' => Carbon::parse($adset['end_time']),
                    'daily_budget' => $adset['daily_budget'] ?? null,
                    'bid_amount' => $adset['bid_amount'] ?? null,
                    'status' => $adset['effective_status'] ?? null,
                    'live_status' => $adset['status'],
                    'created_at' => Carbon::parse($adset['created_time']),
                ]);
            }
        }
    }
}
