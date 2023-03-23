<?php

namespace App\Http\Controllers;

use App\Setting;
use Exception;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V12\Common\ManualCpc;
use Google\Ads\GoogleAds\V12\Common\FrequencyCapEntry;
use Google\Ads\GoogleAds\V12\Common\FrequencyCapKey;
use Google\Ads\GoogleAds\V12\Common\TargetCpa;
use Google\Ads\GoogleAds\V12\Common\TargetRoas;
use Google\Ads\GoogleAds\V12\Common\TargetSpend;
use Google\Ads\GoogleAds\V12\Common\MaximizeConversionValue;
use Google\Ads\GoogleAds\V12\Enums\FrequencyCapLevelEnum\FrequencyCapLevel;
use Google\Ads\GoogleAds\V12\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V12\Enums\FrequencyCapTimeUnitEnum\FrequencyCapTimeUnit;
use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V12\Enums\FrequencyCapEventTypeEnum\FrequencyCapEventType;
use Google\Ads\GoogleAds\V12\Enums\PositiveGeoTargetTypeEnum\PositiveGeoTargetType;
use Google\Ads\GoogleAds\V12\Enums\NegativeGeoTargetTypeEnum\NegativeGeoTargetType;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelSubTypeEnum\AdvertisingChannelSubType;
use Google\Ads\GoogleAds\V12\Enums\AppCampaignBiddingStrategyGoalTypeEnum\AppCampaignBiddingStrategyGoalType;
use Google\Ads\GoogleAds\V12\Enums\OptimizationGoalTypeEnum\OptimizationGoalType;
use Google\Ads\GoogleAds\V12\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V12\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Resources\Campaign;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V12\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V12\Resources\Campaign\GeoTargetTypeSetting;
use Google\Ads\GoogleAds\V12\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V12\Resources\Campaign\ShoppingSetting;
use Google\Ads\GoogleAds\V12\Resources\Campaign\AppCampaignSetting;
use Google\Ads\GoogleAds\V12\Resources\Campaign\OptimizationGoalSetting;
use Google\Ads\GoogleAds\V12\Enums\AppCampaignAppStoreEnum\AppCampaignAppStore;
use Google\Ads\GoogleAds\V12\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;
use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Illuminate\Http\Request;
use Google\Protobuf\Int32Value;

use App\Models\GoogleAdGroupKeyword;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleResponsiveDisplayAdMarketingImage;
use App\Models\GoogleAppAd;
use App\Models\GoogleAppAdImage;
use App\GoogleAd;
use App\GoogleAdsGroup;

use App\Helpers\GoogleAdsHelper;

class GoogleCampaignsController extends Controller
{
    // show campaigns in main page
    public $exceptionError = 'Something went wrong';

    public function getstoragepath($account_id)
    {
        $result = \App\GoogleAdsAccount::find($account_id);
        if (isset($result->config_file_path) && $result->config_file_path != '' && \Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = \Storage::disk('adsapi')->url($account_id.'/'.$result->config_file_path);
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);
            /* echo $storagepath; exit;
        echo storage_path('adsapi_php.ini'); exit; */
            /* echo '<pre>' . print_r($result, true) . '</pre>';
            die('developer working'); */
            return $storagepath;
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
    }

    public function campaignslist(Request $request)
    {
        $search_data = \App\GoogleAdsCampaign::has('account')->with('account')->latest()->get();
        $campaignslist = \App\GoogleAdsCampaign::has('account')->with('account')->latest();

        if(!empty($request->account_name))
        {
            $campaignslist->where('account_id',$request->account_name);
        }
        if(!empty($request->campaign_name))
        {
            $campaignslist->Where('campaign_name',$request->campaign_name);
        }
        if(!empty($request->channel_type))
        {
            $campaignslist->where('channel_type','like', '%'.$request->channel_type.'%');
        }
        if(!empty($request->channel_sub_type))
        {
            $campaignslist->where('channel_sub_type','like', '%'.$request->channel_sub_type.'%');
        }
        if(!empty($request->status))
        {
            $campaignslist->where('status','like', '%'.$request->status.'%');
        }
        if(!empty($request->start_date))
        {
            $campaignslist->where('start_date','like', '%'.$request->start_date.'%');
        }
        if(!empty($request->end_date))
        {
            $campaignslist->where('end_date','like', '%'.$request->end_date.'%');
        }



        $campaignslist = $campaignslist->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($campaignslist);

        return view('googlecampaigns.google_campaignslist', compact('campaignslist','totalNumEntries','search_data'));
    }

    public function adslist(Request $request)
    {
        $search_data = \App\GoogleAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $adslist = \App\GoogleAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if(!empty($request->campaign_name))
        {
            $adslist->where('adgroup_google_campaign_id',$request->campaign_name);
        }
        if(!empty($request->ad_group_name))
        {
            $adslist->where('google_adgroup_id',$request->ad_group_name);
        }
        if(!empty($request->google_ad_id))
        {
            $adslist->where('google_ad_id',$request->google_ad_id);
        }

        $adslist = $adslist->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($adslist);

        return view('googleads.ads_list', compact('adslist','totalNumEntries','search_data'));
    }

    public function appadlist(Request $request)
    {
        $search_data =  \App\Models\GoogleAppAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $googleappadd = \App\Models\GoogleAppAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if(!empty($request->campaign_name))
        {
            $googleappadd->where('adgroup_google_campaign_id',$request->campaign_name);
        }
        if(!empty($request->google_adgroup_id))
        {
            $googleappadd->where('google_adgroup_id',$request->google_adgroup_id);
        }
        if(!empty($request->headline1))
        {
            $googleappadd->where('headline1',$request->headline1);
        }

        $googleappadd = $googleappadd->paginate(10)->appends(request()->except(['page']));

        $totalentries = $googleappadd->count();
        return view('google_app_ad.appaddlist' , compact('googleappadd' , 'totalentries','search_data'));
    }

    public function display_ads(Request $request)
    {
        $search_data = GoogleResponsiveDisplayAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest()->get();
        $display_ads = GoogleResponsiveDisplayAd::has('adgroup')->with('adgroup', 'campaign', 'campaign.account')->latest();

        if(!empty($request->campaign_name))
        {
            $display_ads->where('adgroup_google_campaign_id',$request->campaign_name);
        }
        if(!empty($request->google_adgroup_id))
        {
            $display_ads->where('google_adgroup_id',$request->google_adgroup_id);
        }
        if(!empty($request->headline1))
        {
            $display_ads->where('headline1',$request->headline1);
        }

        $display_ads = $display_ads->paginate(10)->appends(request()->except(['page']));

        $totalNumEntries = count($display_ads);

        return view('google_responsive_display_ad.displayads_list', compact('display_ads','totalNumEntries','search_data'));
    }

    public function adsgroupslist(Request $request)
    {
        $search_data = \App\GoogleAdsGroup::has('campaign')->with('campaign', 'campaign.account')->latest()->get();
        $adsgroups = \App\GoogleAdsGroup::has('campaign')->with('campaign', 'campaign.account')->latest();

        if(!empty($request->campaign_name))
        {
            $adsgroups->where('adgroup_google_campaign_id',$request->campaign_name);
        }
        if(!empty($request->ad_group_name))
        {
            $adsgroups->where('ad_group_name',$request->ad_group_name);
        }
        if(!empty($request->created_at))
        {
            $adsgroups->where('created_at','like', '%'.$request->created_at.'%');
        }

        $adsgroups = $adsgroups->paginate(10)->appends(request()->except(['page']));

        $totalentries = $adsgroups->count();
        return view('googleadgroups.grouplist' , compact('adsgroups' , 'totalentries','search_data'));
    }

    public function index(Request $request)
    {

        if ($request->get('account_id')) {
            $account_id = $request->get('account_id');
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
        // $storagepath = $this->getstoragepath($account_id);
        //echo $storagepath; exit;
        //echo $storagepath; exit;
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($storagepath)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile($storagepath)
            ->withOAuth2Credential($oAuth2Credential)
            ->build(); */

        $query = \App\GoogleAdsCampaign::query();
        if ($request->googlecampaign_id) {
            $query = $query->where('google_campaign_id', $request->googlecampaign_id);
        }
        if ($request->googlecampaign_name) {
            $query = $query->where('campaign_name', 'LIKE', '%'.$request->googlecampaign_name.'%');
        }

        if ($request->googlecampaign_budget) {
            $query = $query->where('budget_amount', 'LIKE', '%'.$request->googlecampaign_budget.'%');
        }
        if ($request->start_date) {
            $query = $query->where('start_date', 'LIKE', '%'.$request->start_date.'%');
        }
        if ($request->end_date) {
            $query = $query->where('end_date', 'LIKE', '%'.$request->end_date.'%');
        }
        if ($request->budget_uniq_id) {
            $query = $query->where('budget_uniq_id', $request->budget_uniq_id);
        }

        if ($request->campaign_status) {
            $query = $query->where('status', $request->campaign_status);
        }

        $query->where('account_id', $account_id);
        $campInfo = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googlecampaigns.partials.list-adscampaign', ['campaigns' => $campInfo])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $campInfo->render(),
                'count' => $campInfo->total(),
            ], 200);
        }

        $totalEntries = $campInfo->count();

        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Campaign',
                    'message' => "Viewed campaign listing"
                );
        insertGoogleAdsLog($input);

        return view('googlecampaigns.index', ['campaigns' => $campInfo, 'totalNumEntries' => $totalEntries, 'biddingStrategyTypes' => $biddingStrategyTypes]);
        /*$adWordsServices = new AdWordsServices();
         $campInfo = $this->getCampaigns($adWordsServices, $session);
        return view('googlecampaigns.index', ['campaigns' => $campInfo['campaigns'], 'totalNumEntries' => $campInfo['totalNumEntries']]); */
    }

    // get campaigns and total count
    public function getCampaigns(AdWordsServices $adWordsServices, AdWordsSession $session)
    {
        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector.
        $campaignSelector = new Selector();
        $campaignSelector->setFields(['Id', 'Name', 'Status', 'BudgetId', 'BudgetName', 'Amount']);
        $campaignSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $campaignSelector->setPaging(new Paging(0, 10));

        $adGroupService = $adWordsServices->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        $groupSelector = new Selector();
        $groupSelector->setFields(['Id', 'Name']);
        $groupSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $groupSelector->setPaging(new Paging(0, 10));

        //        $budgetService = $adWordsServices->get($session, BudgetService::class);
        $totalNumEntries = 0;
        $campaigns = [];
        do {
            // Make the get request.
            $page = $campaignService->get($campaignSelector);
            // Display results.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $campaign) {
                    // getting campaign's adgroups
                    $groupSelector->setPredicates(
                        [new Predicate('CampaignId', PredicateOperator::IN, [$campaign->getId()])]
                    );
                    $adGroupPage = $adGroupService->get($groupSelector);
                    $adGroups = [];
                    if ($adGroupPage->getEntries() !== null) {
                        //                        $totalNumEntries = $page->getTotalNumEntries();
                        foreach ($adGroupPage->getEntries() as $adGroup) {
                            $adGroups[] = [
                                'adGroupId' => $adGroup->getId(),
                                'adGroupName' => $adGroup->getName(),
                            ];
                        }
                    }
                    // getting budget
                    $campaignBudget = $campaign->getBudget();
                    // adding new campaign
                    $campaigns[] = [
                        'campaignId' => $campaign->getId(),
                        'campaignGroups' => $adGroups,
                        'name' => $campaign->getName(),
                        'status' => $campaign->getStatus(),
                        'budgetId' => $campaignBudget->getBudgetId(),
                        'budgetName' => $campaignBudget->getName(),
                        'budgetAmount' => $campaignBudget->getAmount()->getMicroAmount() / 1000000,
                    ];
                }
            }

            // Advance the paging index.
            $campaignSelector->getPaging()->setStartIndex(
                $campaignSelector->getPaging()->getStartIndex() + 10
            );
        } while ($campaignSelector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'totalNumEntries' => $totalNumEntries,
            'campaigns' => $campaigns,
        ];
    }

    // go to create page
    public function createPage()
    {
        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Campaign',
                    'message' => "Viewed create campaign"
                );
        insertGoogleAdsLog($input);

        return view('googlecampaigns.create', compact('biddingStrategyTypes'));
    }

    // create campaign
    public function createCampaign(Request $request)
    {
        $account_id = $request->account_id;
        $account = \App\GoogleAdsAccount::findOrFail($account_id);
        $customerId = $account->google_customer_id;

        $this->validate($request, [
            'campaignName' => 'required|max:55',
            'channel_type' => 'required',
            'budgetAmount' => 'required|max:55',
            'start_date' => 'required|max:15',
            'end_date' => 'required|max:15',
        ]);
        try {
            $campaignArray = [];
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $budgetAmount = $request->budgetAmount;
            $campaignName = $request->campaignName;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];

            //start creating array to store data into database
            $campaignArray['account_id'] = $account_id;
            $campaignArray['google_customer_id'] = $customerId;

            // $storagepath = $this->getstoragepath($account_id);
            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;
            if ($request->channel_type) {
                $channel_type = $request->channel_type;
            } else {
                $channel_type = 'SEARCH';
            }
            $campaignArray['channel_type'] = $channel_type;

            if ($request->channel_sub_type) {
                $channel_sub_type = $request->channel_sub_type;
            } else {
                $channel_sub_type = 'UNKNOWN';
            }
            $campaignArray['channel_sub_type'] = $channel_sub_type;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;

            if ($request->ad_rotation) {
                $ad_rotation = $request->ad_rotation;
            } else {
                $ad_rotation = '';
            }
            $campaignArray['ad_rotation'] = $ad_rotation;

            if ($request->tracking_template_url) {
                $tracking_template_url = $request->tracking_template_url;
            } else {
                $tracking_template_url = '';
            }
            $campaignArray['tracking_template_url'] = $tracking_template_url;

            if ($request->final_url_suffix) {
                $final_url_suffix = $request->final_url_suffix;
            } else {
                $final_url_suffix = '';
            }
            $campaignArray['final_url_suffix'] = $final_url_suffix;

            if ($request->merchant_id) {
                $merchant_id = $request->merchant_id;
            } else {
                $merchant_id = '';
            }
            $campaignArray['merchant_id'] = $merchant_id;

            if ($request->sales_country) {
                $sales_country = $request->sales_country;
            } else {
                $sales_country = '';
            }
            $campaignArray['sales_country'] = $sales_country;
            
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            $budget = self::addCampaignBudget($googleAdsClient, $customerId, $budgetAmount, $channel_type);
            $campaignArray['budget_uniq_id'] = $budget['budget_uniq_id'] ?? null;
            $campaignArray['budget_id'] = $budget['budget_id'] ?? null;
            $budgetResourceName = $budget['budget_resource_name'] ?? null;

            // Creates a campaign.
            $campaignArr = array(
                                'name' => $campaignName,
                                'campaign_budget' => $budgetResourceName,
                                'status' => self::getCampaignStatus($campaignStatus),
                                'advertising_channel_type' => self::getAdvertisingChannelType($channel_type),
                                'network_settings' => self::getNetworkSettings($channel_type, $channel_sub_type),
                                'shopping_setting' => ($channel_type === 'SHOPPING') ? self::getShoppingSetting($merchant_id, $sales_country) : null,
                                // 'frequency_caps' => self::getFrequencyCaps(),
                                'geo_target_type_setting' => self::getGeoTargetTypeSetting(),
                                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                                'start_date' => $campaign_start_date,
                                'end_date' => $campaign_end_date,
                            );
            

            if($channel_type == "PERFORMANCE_MAX"){
                $campaignArr['url_expansion_opt_out'] = false;
                unset($campaignArr['advertising_channel_sub_type']);
            }else{
                $campaignArr['advertising_channel_sub_type'] = self::getAdvertisingChannelSubType($channel_sub_type);
            }


            if(!empty($final_url_suffix)){
                $campaignArr['final_url_suffix'] = $final_url_suffix;
            }


            if($channel_type == "MULTI_CHANNEL"){
                $campaignArray['app_id'] = $request->app_id;
                $campaignArray['app_store'] = $request->app_store;
                $campaignArr['app_campaign_setting'] = self::getAppCampaignSetting($campaignArray['app_id'], $campaignArray['app_store'], $channel_sub_type);
                unset($campaignArr['bidding_strategy_type']);
                unset($campaignArr['network_settings']);


                if (in_array($bidding_strategy_type, ['TARGET_CPA']) && !$txt_target_cpa) {
                   $txt_target_cpa = 1;
                   $campaignArray['target_cpa_value'] = $txt_target_cpa;
                }

                if($channel_sub_type == "APP_CAMPAIGN_FOR_PRE_REGISTRATION"){
                    $campaignArr['optimization_goal_setting'] = new OptimizationGoalSetting([
                                                                        'optimization_goal_types' => [ 
                                                                            OptimizationGoalType::APP_PRE_REGISTRATION 
                                                                        ]
                                                                    ]);
                }
            }


            if (in_array($bidding_strategy_type, ['TARGET_CPA']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            }else if (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            }else if (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            }else if($channel_type == "PERFORMANCE_MAX"  && $txt_target_roas){
                $campaignArr['maximize_conversion_value'] = new MaximizeConversionValue(['target_roas' => $txt_target_roas]);
            }else if($channel_type != "MULTI_CHANNEL"){
                $campaignArr['manual_cpc'] = new ManualCpc();
            }


            $campaign = new Campaign($campaignArr);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);

            // Submits the campaign operation and prints the results.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);

            $createdCampaign = $response->getResults()[0];
            $campaignResourceName = $createdCampaign->getResourceName();

            $campaignArray['google_campaign_id'] = substr($campaignResourceName, strrpos($campaignResourceName, "/") + 1);
            $campaignArray['campaign_response'] = json_encode($createdCampaign);
            \App\GoogleAdsCampaign::create($campaignArray);
            
            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Campaign',
                        'message' => "Created new campaign",
                        'response' => json_encode($campaignArray)
                    );
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign created successfully');
        } catch (Exception $e) {
            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Campaign',
                        'message' => 'Create new campaign > '. $e->getMessage(),
                    );
            insertGoogleAdsLog($input);
            return response()->json(['status'=> false, "message" => $e->getMessage()]);
            // return redirect()->to('google-campaigns/create?account_id='.$request->account_id)->with('actError', $e->getMessage());
        }
    }

    // go to update page
    public function updatePage(Request $request, $campaignId)
    {
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector.
        $campaignSelector = new Selector();
        $campaignSelector->setFields(['Id', 'Name', 'Status']);
        //        $campaignSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        //        $campaignSelector->setPaging(new Paging(0, 10));
        $campaignSelector->setPredicates(
            [new Predicate('Id', PredicateOperator::IN, [$campaignId])]
        );

        $page = $campaignService->get($campaignSelector);
        $pageEntries = $page->getEntries();

        if ($pageEntries !== null) {
            $campaign = $pageEntries[0];
        }
        $campaign = [
            "campaignId" => $campaign->getId(),
            //            "campaignGroups" => $adGroups,
            "name" => $campaign->getName(),
            "status" => $campaign->getStatus(),
            //                        "budgetId" => $campaignBudget->getBudgetId(),
            //                        "budgetName" => $campaignBudget->getName(),
            //                        "budgetAmount" => $campaignBudget->getAmount()
        ];
        // */
        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();
        $campaign = \App\GoogleAdsCampaign::where('google_campaign_id', $campaignId)->firstOrFail();

         // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Campaign',
                    'message' => "Viewed update campaign for ". $campaign->name
                );
        insertGoogleAdsLog($input);

        return view('googlecampaigns.update', ['campaign' => $campaign, 'biddingStrategyTypes' => $biddingStrategyTypes]);
    }

    // save campaign's changes
    public function updateCampaign(Request $request)
    {
        $this->validate($request, [
            'campaignName' => 'required|max:55',
            'budgetAmount' => 'required|max:55',
            'start_date' => 'required|max:15',
            'end_date' => 'required|max:15',
        ]);

        $campaignDetail = \App\GoogleAdsCampaign::where('google_campaign_id',
            $request->campaignId)->first();
        $account_id = $campaignDetail->account_id;
        $customerId = $campaignDetail->google_customer_id;
        try {
            // $storagepath = $this->getstoragepath($account_id);
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $campaignId = $request->campaignId;
            $campaignName = $request->campaignName;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];

            $campaignArray = [];
            $budgetAmount = $request->budgetAmount;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;

            //start creating array to store data into database

            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;

            
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            $budget = self::updateCampaignBudget($googleAdsClient, $customerId, $budgetAmount, $campaignDetail->budget_id);
            $budgetResourceName = $budget['budget_resource_name'] ?? null;

            // Creates a campaign.
            $campaignArr = array(
                                'resource_name' => ResourceNames::forCampaign($customerId, $campaignId),
                                
                                'name' => $campaignName,
                                'campaign_budget' => $budgetResourceName,
                                'status' => self::getCampaignStatus($campaignStatus),
                                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                                'start_date' => $campaign_start_date,
                                'end_date' => $campaign_end_date,
                            );

            if($campaignDetail->channel_type == "MULTI_CHANNEL"){
                if (!in_array($campaignDetail->bidding_strategy_type, ['TARGET_CPA']) || !$txt_target_cpa) {
                   $txt_target_cpa = 1;
                   $bidding_strategy_type = "TARGET_CPA";
                   $campaignArray['target_cpa_value'] = $txt_target_cpa;
                   $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;
                }
            }

            if (in_array($bidding_strategy_type, ['TARGET_CPA', 'MAXIMIZE_CONVERSION_VALUE']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            }

            if (in_array($bidding_strategy_type, ['MANUAL_CPC'])) {
                $campaignArr['target_cpa'] = null;
                $campaignArr['target_spend'] = null;
                $campaignArr['target_roas'] = null;
            }

            // Creates a campaign object with the specified resource name and other changes.
            $campaign = new Campaign($campaignArr);

            // Constructs an operation that will update the campaign with the specified resource name,
            // using the FieldMasks utility to derive the update mask. This mask tells the Google Ads
            // API which attributes of the campaign you want to change.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

            // Issues a mutate request to update the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns(
                $customerId,
                [$campaignOperation]
            );

            $updatedCampaign = $response->getResults()[0];
            $campaignResourceName = $updatedCampaign->getResourceName();

            $campaignArray['google_campaign_id'] = substr($campaignResourceName, strrpos($campaignResourceName, "/") + 1);
            $campaignArray['campaign_response'] = json_encode($updatedCampaign);
            \App\GoogleAdsCampaign::whereId($campaignDetail->id)->update($campaignArray);

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Campaign',
                        'message' => 'Updated campaign details for '. $campaignName,
                    );
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign updated successfully');
        } catch (Exception $e) {
            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Campaign',
                        'message' => 'Update campaign > '. $e->getMessage(),
                    );
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns/update/'.$request->campaignId.'?account_id='.$account_id)->with('actError', $e->getMessage());
        }
    }

    // delete campaign
    public function deleteCampaign(Request $request, $campaignId)
    {
        try {
            
            $account_id = $request->delete_account_id;
            $googleAdsCampaign = \App\GoogleAdsCampaign::where('account_id', $account_id)->where('google_campaign_id', $campaignId)->firstOrFail();
            $customerId = $googleAdsCampaign->google_customer_id;

            // $storagepath = $this->getstoragepath($account_id);
                        
            // Generate a refreshable OAuth2 credential for authentication.
            $googleAdsClient = GoogleAdsHelper::getGoogleAdsClient($account_id);

            // Creates the resource name of a campaign to remove.
            $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setRemove($campaignResourceName);

            // Issues a mutate request to remove the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Campaign',
                        'message' => 'Deleted campaign',
                        'response' => json_encode($googleAdsCampaign)
                    );

            // Delete other data
            GoogleAdGroupKeyword::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleResponsiveDisplayAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleResponsiveDisplayAdMarketingImage::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAppAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAppAdImage::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAd::where('adgroup_google_campaign_id', $campaignId)->delete();
            GoogleAdsGroup::where('adgroup_google_campaign_id', $campaignId)->delete();

            $googleAdsCampaign->delete();

            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Campaign',
                        'message' => 'Delete campaign > ' . $e->getMessage(),
                    );
            insertGoogleAdsLog($input);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actError', $this->exceptionError);
        }
    }

    // create a campaign single shared budget
    public function addCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $channelType)
    {
        $response = [];
        try {
            $uniqId = uniqid();

            // Creates a campaign budget.
            $budgetArr = [
                'name' => 'Interplanetary Cruise Budget #' . $uniqId,
                'delivery_method' => BudgetDeliveryMethod::STANDARD,
                'amount_micros' => $amount * 1000000
            ];

            if(in_array($channelType, ["PERFORMANCE_MAX", "MULTI_CHANNEL"])){
                // A Performance Max campaign cannot use a shared campaign budget.
                $budgetArr['explicitly_shared'] = false;
            }

            $budget = new CampaignBudget($budgetArr);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setCreate($budget);

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $createdBudget = $response->getResults()[0];
            $budgetResourceName = $createdBudget->getResourceName();

            $response = array(
                            'budget_uniq_id' => $uniqId,
                            'budget_id' => substr($budgetResourceName, strrpos($budgetResourceName, "/") + 1),
                            'budget_resource_name' => $budgetResourceName,
                        );
        } catch (Exception $e) {
            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Campaign',
                        'message' => 'Create campaign budget > '. $e->getMessage(),
                    );
            insertGoogleAdsLog($input);        
        }

        return $response;
    }

    // update a campaign single shared budget
    public function updateCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $campaignBudgetId)
    {
        $response = [];
        try {
            // Creates a campaign budget.
            $budget = new CampaignBudget([
                            'resource_name' => ResourceNames::forCampaignBudget($customerId, $campaignBudgetId),

                            'amount_micros' => $amount * 1000000
                        ]);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setUpdate($budget);
            $campaignBudgetOperation->setUpdateMask(FieldMasks::allSetFieldsOf($budget));

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $updatedBudget = $response->getResults()[0];
            $budgetResourceName = $updatedBudget->getResourceName();

            $response = array(
                            'budget_id' => substr($budgetResourceName, strrpos($budgetResourceName, "/") + 1),
                            'budget_resource_name' => $budgetResourceName,
                        );
        } catch (Exception $e) {
            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Campaign',
                        'message' => 'Update campaign budget > '. $e->getMessage(),
                    );
            insertGoogleAdsLog($input);     
        }

        return $response;
    }

    //function to retrieve data from library
    //get advertising channel type
    public function getAdvertisingChannelType($v)
    {
        switch ($v) {
            case 'SEARCH':
                return AdvertisingChannelType::SEARCH;
                break;

            case 'DISPLAY':
                return AdvertisingChannelType::DISPLAY;
                break;

            case 'SHOPPING':
                return AdvertisingChannelType::SHOPPING;
                break;

            case 'MULTI_CHANNEL':
                return AdvertisingChannelType::MULTI_CHANNEL;
                break;

            case 'PERFORMANCE_MAX':
                return AdvertisingChannelType::PERFORMANCE_MAX;
                break;

            case 'UNKNOWN':
                return AdvertisingChannelType::UNKNOWN;
                break;

            default:
                return AdvertisingChannelType::SEARCH;
        }
    }

    //get advertising sub type
    private function getAdvertisingChannelSubType($v)
    {
        switch ($v) {
            case 'UNSPECIFIED':
                return AdvertisingChannelSubType::UNSPECIFIED;
                break;

            case 'SEARCH_MOBILE_APP':
                return AdvertisingChannelSubType::SEARCH_MOBILE_APP;
                break;

            case 'DISPLAY_MOBILE_APP':
                return AdvertisingChannelSubType::DISPLAY_MOBILE_APP;
                break;

            case 'SEARCH_EXPRESS':
                return AdvertisingChannelSubType::SEARCH_EXPRESS;
                break;

            case 'DISPLAY_EXPRESS':
                return AdvertisingChannelSubType::DISPLAY_EXPRESS;
                break;

            case 'DISPLAY_SMART_CAMPAIGN':
                return AdvertisingChannelSubType::DISPLAY_SMART_CAMPAIGN;
                break;

            case 'SHOPPING_GOAL_OPTIMIZED_ADS':
                return AdvertisingChannelSubType::SHOPPING_GOAL_OPTIMIZED_ADS;
                break;

            case 'DISPLAY_GMAIL_AD':
                return AdvertisingChannelSubType::DISPLAY_GMAIL_AD;
                break;

            case 'APP_CAMPAIGN':
                return AdvertisingChannelSubType::APP_CAMPAIGN;
                break;

            case 'APP_CAMPAIGN_FOR_ENGAGEMENT':
                return AdvertisingChannelSubType::APP_CAMPAIGN_FOR_ENGAGEMENT;
                break;

            case 'APP_CAMPAIGN_FOR_PRE_REGISTRATION':
                return AdvertisingChannelSubType::APP_CAMPAIGN_FOR_PRE_REGISTRATION;
                break;

            default:
                return AdvertisingChannelSubType::UNSPECIFIED;
        }
    }

    public function getBiddingStrategyTypeArray()
    {
        // return ['MANUAL_CPC' => 'Manually set bids', 'MANUAL_CPM' => 'Viewable CPM', 'PAGE_ONE_PROMOTED' => 'Page one promoted', 'TARGET_SPEND' => 'Maximize clicks', 'TARGET_CPA' => 'Target CPA', 'TARGET_ROAS' => 'Target Roas', 'MAXIMIZE_CONVERSIONS' => 'max conv', 'MAXIMIZE_CONVERSION_VALUE' => 'Automatically maximize conversions', 'TARGET_OUTRANK_SHARE' => 'Target outrank sharing', 'NONE' => 'None', 'UNKNOWN' => 'Unknown'];
        return [
            'MANUAL_CPC' => 'Manually set bids', 
            'MANUAL_CPM' => 'Viewable CPM', 
            'TARGET_SPEND' => 'Maximize clicks', 
            'TARGET_CPA' => 'Target CPA', 
            'TARGET_ROAS' => 'Target Roas', 
            'MAXIMIZE_CONVERSIONS' => 'Maximise conversions', 
            'MAXIMIZE_CONVERSION_VALUE' => 'Automatically maximize conversions', 
            'UNSPECIFIED' => 'Unspecified'
        ];
    }

    // get network settings
    private function getNetworkSettings($channel_type, $channel_sub_type)
    {

        $networkSettingsArr = array(
                                'target_google_search' => false,
                                'target_search_network' => false,
                                'target_content_network' => false,
                                'target_partner_search_network' => false
                            );

        if ($channel_type == 'SEARCH' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS')) {

            $networkSettingsArr['target_google_search'] = true;

        } elseif ($channel_type == 'MULTI_CHANNEL' || $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS') {

            $networkSettingsArr['target_search_network'] = true;

        }

        if ($channel_type == 'DISPLAY' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'DISPLAY_SMART_CAMPAIGN')) {

            $networkSettingsArr['target_content_network'] = true;

        }

        if ($channel_type == 'SHOPPING') {

            $networkSettingsArr['target_google_search'] = true;
            $networkSettingsArr['target_search_network'] = true;

        }

        if ($channel_type == 'MULTI_CHANNEL') {

            $networkSettingsArr['target_partner_search_network'] = true;

        }
        $networkSettings = new NetworkSettings($networkSettingsArr);

        return $networkSettings;
    }

    // get shopping setting
    private function getShoppingSetting($merchant_id, $sales_country)
    {
        $shoppingSetting = new ShoppingSetting([
            'sales_country' => $sales_country,
            'campaign_priority' => 0,
            'merchant_id' => $merchant_id,
            'enable_local' => true
        ]);

        return $shoppingSetting;
    }

    //get frequency caps
    private function getFrequencyCaps()
    {
        $frequencyCaps = new FrequencyCapEntry([
            'key' => new FrequencyCapKey([
                'level'=> FrequencyCapLevel::AD_GROUP,
                'event_type'=> FrequencyCapEventType::IMPRESSION,
                'time_unit'=> FrequencyCapTimeUnit::DAY,
            ]),
            'cap' => intval(5), /*new Int32Value(['value'=> intval(5)])*/
        ]);
    }

    // get shopping setting
    private function getGeoTargetTypeSetting()
    {

        $shoppingSetting = new GeoTargetTypeSetting([
            'positive_geo_target_type' => PositiveGeoTargetType::UNSPECIFIED,
            'negative_geo_target_type' => NegativeGeoTargetType::UNSPECIFIED,
        ]);

        return $shoppingSetting;
    }

    //get bidding strategy type
    private function getBiddingStrategyType($v)
    {
        switch ($v) {
            case 'MANUAL_CPC':
                return BiddingStrategyType::MANUAL_CPC;
                break;

            case 'MANUAL_CPM':
                return BiddingStrategyType::MANUAL_CPM;
                break;

            case 'PAGE_ONE_PROMOTED':
                return BiddingStrategyType::PAGE_ONE_PROMOTED;
                break;

            case 'TARGET_SPEND':
                return BiddingStrategyType::TARGET_SPEND;
                break;

            case 'TARGET_CPA':
                return BiddingStrategyType::TARGET_CPA;
                break;

            case 'TARGET_ROAS':
                return BiddingStrategyType::TARGET_ROAS;
                break;

            case 'MAXIMIZE_CONVERSIONS':
                return BiddingStrategyType::MAXIMIZE_CONVERSIONS;
                break;

            case 'MAXIMIZE_CONVERSION_VALUE':
                return BiddingStrategyType::MAXIMIZE_CONVERSION_VALUE;
                break;

            case 'TARGET_OUTRANK_SHARE':
                return BiddingStrategyType::TARGET_OUTRANK_SHARE;
                break;

            case 'NONE':
                return BiddingStrategyType::NONE;
                break;

            default:
                return BiddingStrategyType::UNKNOWN;
        }
    }

    //get campaign status  
    private function getCampaignStatus($v)
    {
        switch ($v) {
            case 'ENABLED':
                return CampaignStatus::ENABLED;
                break;

            case 'PAUSED':
                return CampaignStatus::PAUSED;
                break;

            case 'REMOVED':
                return CampaignStatus::REMOVED;
                break;

            default:
                return CampaignStatus::PAUSED;
        }
    }

    //get app campaign setting
    private function getAppCampaignSetting($appId, $appStore, $channel_sub_type){

        $appStore = ($appStore == "GOOGLE_APP_STORE" ? AppCampaignAppStore::GOOGLE_APP_STORE : AppCampaignAppStore::APPLE_APP_STORE);

        $bidding_strategy_type = AppCampaignBiddingStrategyGoalType::OPTIMIZE_INSTALLS_TARGET_INSTALL_COST;
        if($channel_sub_type == "APP_CAMPAIGN_FOR_PRE_REGISTRATION"){
            $bidding_strategy_type = AppCampaignBiddingStrategyGoalType::OPTIMIZE_PRE_REGISTRATION_CONVERSION_VOLUME;
        }

        $appCampaignSetting = new AppCampaignSetting([
                'app_id' => $appId,
                'app_store' => $appStore,
                'bidding_strategy_goal_type' => $bidding_strategy_type
            ]);

        return $appCampaignSetting;
    }
}
