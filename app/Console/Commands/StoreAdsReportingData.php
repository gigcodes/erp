<?php

namespace App\Console\Commands;

use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsReporting;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V12\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V12\Services\GoogleAdsRow;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Config\Definition\Exception\Exception;


class StoreAdsReportingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:ads-reporting-data';

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
     * @return int
     */
    public function handle()
    {
        $googleAdsAccounts = GoogleAdsAccount::with(['campaigns'])->get();
        foreach ($googleAdsAccounts as $googleAdsAccount){
            $this->getAllCampaignData($googleAdsAccount);
        }
        $this->info('test-');
        return 0;
    }

    /**
     * @param $googleAdsCampaign
     * @throws \Google\ApiCore\ApiException
     */
    public function getAllCampaignData($googleAdsAccount)
    {
        $storagepath = storage_path('app/adsapi/'.$googleAdsAccount->id.'/'.$googleAdsAccount->config_file_path);        // Get OAuth2 configuration from file.

        $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->from($oAuth2Configuration)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();

//        $campaignIds = $googleAdsAccount->campaigns->pluck('google_campaign_id')->toArray();
//        $campaignIdsVal = implode(',',$campaignIds);

        $query = "SELECT metrics.impressions,
            metrics.clicks,
            campaign.id,
            campaign.name,
            metrics.ctr,
            metrics.average_cpc FROM keyword_view";

        // Issues a search stream request.
        /** @var GoogleAdsServerStreamDecorator $stream */
        $stream = $googleAdsServiceClient->search($googleAdsAccount->google_customer_id, $query);

        // Iterates over all rows in all messages and prints the requested field values for
        // the keyword in each row.
        foreach ($stream->iterateAllElements() as $googleAdsRow) {

            /** @var GoogleAdsRow $googleAdsRow */
            $metrics = $googleAdsRow->getMetrics();
            $campaign = $googleAdsRow->getCampaign();

            try {
                \DB::enableQueryLog();

                $exists_compaign = GoogleAdsCampaign::all();
                if($exists_compaign->isEmpty()){
                    dd("Compaign not found::");
                }else{
//                  $compaign_list = GoogleAdsCampaign::where('account_id',$googleAdsAccount->id)->first();
//                  dd($compaign_list->google_campaign_id);
                    $exists_account = GoogleAdsReporting::where('google_account_id',$googleAdsAccount->id)->exists();
                    if($exists_account){
                        $store_reporting = GoogleAdsReporting::where('google_account_id', '=',  $googleAdsAccount->id)->first();
                        $store_reporting->google_account_id = $googleAdsAccount->id;
                        $store_reporting->name = $campaign->getName();
                        $store_reporting->impression = $metrics->getImpressions();
                        $store_reporting->click = $metrics->getClicks();
                        $store_reporting->cost_micros = $metrics->getCostMicros();
                        $store_reporting->save();
                    }else{
                        $store_reporting = New GoogleAdsReporting();
                        $store_reporting->google_account_id = $googleAdsAccount->id;
                        $store_reporting->name = $campaign->getName();
                        $store_reporting->impression = $metrics->getImpressions();
                        $store_reporting->click = $metrics->getClicks();
                        $store_reporting->cost_micros = $metrics->getCostMicros();
                        $store_reporting->save();
                    }
                }
            }catch (Exception $exception){
                dd($exception->getMessage());
            }
        }
    }
}
