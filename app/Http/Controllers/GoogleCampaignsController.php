<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\AdGroupService;
use Google\AdsApi\AdWords\v201809\cm\BudgetService;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdWords\v201809\cm\Budget;
use Google\AdsApi\AdWords\v201809\cm\AdvertisingChannelType;
use Google\AdsApi\AdWords\v201809\cm\BiddingStrategyConfiguration;
use Google\AdsApi\AdWords\v201809\cm\BiddingStrategyType;
use Google\AdsApi\AdWords\v201809\cm\BudgetBudgetDeliveryMethod;
use Google\AdsApi\AdWords\v201809\cm\BudgetOperation;
use Google\AdsApi\AdWords\v201809\cm\Campaign;
use Google\AdsApi\AdWords\v201809\cm\CampaignOperation;
use Google\AdsApi\AdWords\v201809\cm\CampaignStatus;
use Google\AdsApi\AdWords\v201809\cm\FrequencyCap;
use Google\AdsApi\AdWords\v201809\cm\GeoTargetTypeSetting;
use Google\AdsApi\AdWords\v201809\cm\GeoTargetTypeSettingNegativeGeoTargetType;
use Google\AdsApi\AdWords\v201809\cm\GeoTargetTypeSettingPositiveGeoTargetType;
use Google\AdsApi\AdWords\v201809\cm\Level;
use Google\AdsApi\AdWords\v201809\cm\ManualCpcBiddingScheme;
use Google\AdsApi\AdWords\v201809\cm\Money;
use Google\AdsApi\AdWords\v201809\cm\NetworkSetting;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\TimeUnit;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;


class GoogleCampaignsController extends Controller
{
    // show campaigns in main page
    public function getstoragepath($account_id)
    {
        $result = \App\GoogleAdsAccount::find($account_id);
        if (isset($result->config_file_path) && $result->config_file_path!='' && \Storage::disk('adsapi')->exists($account_id . '/' . $result->config_file_path)) {
            $storagepath = \Storage::disk('adsapi')->url($account_id . '/' . $result->config_file_path);
            $storagepath = storage_path('app/adsapi/' . $account_id . '/' . $result->config_file_path);
            /* echo $storagepath; exit;
        echo storage_path('adsapi_php.ini'); exit; */
            /* echo '<pre>' . print_r($result, true) . '</pre>';
            die('developer working'); */
            return $storagepath;
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
    }

    public function index(Request $request)
    {
        if($request->get('account_id')){
            $account_id=$request->get('account_id');
        }else{
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
        $storagepath = $this->getstoragepath($account_id);
        //echo $storagepath; exit;
        //echo $storagepath; exit;
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($storagepath)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile($storagepath)
            ->withOAuth2Credential($oAuth2Credential)
            ->build(); */

        
        $campInfo=\App\GoogleAdsCampaign::where('account_id',$account_id)->paginate(15);
        $totalEntries=$campInfo->count();
        return view('googlecampaigns.index', ['campaigns' => $campInfo, 'totalNumEntries' => $totalEntries]);
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
                                'adGroupName' => $adGroup->getName()
                            ];
                        }
                    }
                    // getting budget
                    $campaignBudget = $campaign->getBudget();
                    // adding new campaign
                    $campaigns[] = [
                        "campaignId" => $campaign->getId(),
                        "campaignGroups" => $adGroups,
                        "name" => $campaign->getName(),
                        "status" => $campaign->getStatus(),
                        "budgetId" => $campaignBudget->getBudgetId(),
                        "budgetName" => $campaignBudget->getName(),
                        "budgetAmount" => $campaignBudget->getAmount()->getMicroAmount() / 1000000
                    ];
                }
            }

            // Advance the paging index.
            $campaignSelector->getPaging()->setStartIndex(
                $campaignSelector->getPaging()->getStartIndex() + 10
            );
        } while ($campaignSelector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            "totalNumEntries" => $totalNumEntries,
            "campaigns" => $campaigns
        ];
    }

    // go to create page
    public function createPage()
    {
        //
        return view('googlecampaigns.create');
    }

    // create campaign
    public function createCampaign(Request $request)
    {

        /*  $this->validate($request, [
			'campaignName' => 'required',
			'budgetAmount' => 'required|integer',
			'start_date' => 'required',
			'end_date' => 'required',
			'campaignStatus' => 'required',
		]); */
        $campaignArray = array();
        $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
        $budgetAmount = $request->budgetAmount * 1000000;
        $campaignName = $request->campaignName;
        $campaign_start_date = $request->start_date;
        $campaign_end_date = $request->end_date;
        $campaignStatus = $campaignStatusArr[$request->campaignStatus];

        //start creating array to store data into database
        $account_id = $request->account_id;
        $campaignArray['account_id'] = $account_id;
        $storagepath = $this->getstoragepath($account_id);
        $campaignArray['campaign_name'] = $campaignName;
        $campaignArray['budget_amount'] = $request->budgetAmount;
        $campaignArray['start_date'] = $campaign_start_date;
        $campaignArray['end_date'] = $campaign_end_date;
        $campaignArray['status'] = $campaignStatus;


        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($storagepath)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile($storagepath)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $budgetService = $adWordsServices->get($session, BudgetService::class);

        // Create the shared budget (required).
        $uniq_id = uniqid();
        $campaignArray['budget_uniq_id'] = $uniq_id;
        $budget = new Budget();
        $budget->setName('Interplanetary Cruise Budget #' . $uniq_id);

        $money = new Money();
        $money->setMicroAmount($budgetAmount);
        $budget->setAmount($money);
        $budget->setDeliveryMethod(BudgetBudgetDeliveryMethod::STANDARD);

        $operations = [];

        // Create a budget operation.
        $operation = new BudgetOperation();
        $operation->setOperand($budget);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;

        // Create the budget on the server.
        $result = $budgetService->mutate($operations);

        $budget = $result->getValue()[0];
        
        $campaignService = $adWordsServices->get($session, CampaignService::class);

        $operations = [];

        // Create a campaign with required and optional settings.
        $campaign = new Campaign();
        $campaign->setName($campaignName);
        $campaign->setAdvertisingChannelType(AdvertisingChannelType::SEARCH);

        // Set shared budget (required).
        $campaignArray['budget_id'] = $budget->getBudgetId();
        $campaign->setBudget(new Budget());
        $campaign->getBudget()->setBudgetId($budget->getBudgetId());

        // Set bidding strategy (required).
        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $biddingStrategyConfiguration->setBiddingStrategyType(
            BiddingStrategyType::MANUAL_CPC
        );

        // You can optionally provide a bidding scheme in place of the type.
        $biddingScheme = new ManualCpcBiddingScheme();
        $biddingStrategyConfiguration->setBiddingScheme($biddingScheme);

        $campaign->setBiddingStrategyConfiguration($biddingStrategyConfiguration);

        // Set network targeting (optional).
        $networkSetting = new NetworkSetting();
        $networkSetting->setTargetGoogleSearch(true);
        $networkSetting->setTargetSearchNetwork(true);
        $networkSetting->setTargetContentNetwork(true);
        $campaign->setNetworkSetting($networkSetting);

        // Set additional settings (optional).
        // Recommendation: Set the campaign to PAUSED when creating it to stop
        // the ads from immediately serving. Set to ENABLED once you've added
        // targeting and the ads are ready to serve.
        $campaign->setStatus($campaignStatus); //CampaignStatus::ENABLED);
        // $campaign->setStartDate(date('Ymd', strtotime('+1 day')));
        // $campaign->setEndDate(date('Ymd', strtotime('+1 month')));
        $campaign->setStartDate($campaign_start_date);
        $campaign->setEndDate($campaign_end_date);

        // Set frequency cap (optional).
        $frequencyCap = new FrequencyCap();
        $frequencyCap->setImpressions(5);
        $frequencyCap->setTimeUnit(TimeUnit::DAY);
        $frequencyCap->setLevel(Level::ADGROUP);
        $campaign->setFrequencyCap($frequencyCap);

        // Set advanced location targeting settings (optional).
        $geoTargetTypeSetting = new GeoTargetTypeSetting();
        $geoTargetTypeSetting->setPositiveGeoTargetType(
            GeoTargetTypeSettingPositiveGeoTargetType::DONT_CARE
        );
        $geoTargetTypeSetting->setNegativeGeoTargetType(
            GeoTargetTypeSettingNegativeGeoTargetType::DONT_CARE
        );
        $campaign->setSettings([$geoTargetTypeSetting]);

        // Create a campaign operation and add it to the operations list.
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;

        // Create the campaign on the server
        $result = $campaignService->mutate($operations);
        $addedCampaign = $result->getValue();
        $addedCampaignId = $addedCampaign[0]->getId();
        $campaignArray['google_campaign_id'] = $addedCampaignId;
        $campaignArray['campaign_response'] = json_encode($addedCampaign);
        \App\GoogleAdsCampaign::create($campaignArray);
        /* return redirect()->route('googlecampaigns.index'); */
        return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign created successfully');
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
        $campaign=\App\GoogleAdsCampaign::where('google_campaign_id',$campaignId)->first();
        return view('googlecampaigns.update', ['campaign' => $campaign]);
    }

    // save campaign's changes
    public function updateCampaign(Request $request)
    {
        $campaignDetail=\App\GoogleAdsCampaign::where('google_campaign_id',
        $request->campaignId)->first();
        $account_id=$campaignDetail->account_id;
        $storagepath = $this->getstoragepath($account_id);
        $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
        $campaignId = $request->campaignId;
        $campaignName = $request->campaignName;
        $campaignStatus = $campaignStatusArr[$request->campaignStatus];
        
        $campaignArray = array();
        $budgetAmount = $request->budgetAmount * 1000000;
        $campaign_start_date = $request->start_date;
        $campaign_end_date = $request->end_date;
       
        //start creating array to store data into database
        
        $campaignArray['campaign_name'] = $campaignName;
        $campaignArray['budget_amount'] = $request->budgetAmount;
        $campaignArray['start_date'] = $campaign_start_date;
        $campaignArray['end_date'] = $campaign_end_date;
        $campaignArray['status'] = $campaignStatus;


        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($storagepath)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile($storagepath)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create the shared budget (required).
        $uniq_id = uniqid();
        //$campaignArray['budget_uniq_id'] = $uniq_id;
        $budget = new Budget();
        $budget->setBudgetId($campaignDetail->budget_id);
        //$budget->setName('Interplanetary Cruise Budget #' . $uniq_id);

        $money = new Money();
        $money->setMicroAmount($budgetAmount);
        $budget->setAmount($money);
        $budget->setDeliveryMethod(BudgetBudgetDeliveryMethod::STANDARD);

        $operations = [];
        // Create a campaign with ... status.
        $campaign = new Campaign();
        $campaign->setId($campaignId);
        $campaign->setName($campaignName);
        $campaign->setStatus($campaignStatus);
        $campaign->setStartDate($campaign_start_date);
        $campaign->setEndDate($campaign_end_date);

        // Create a campaign operation and add it to the list.
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Update the campaign on the server.
        $result = $campaignService->mutate($operations);
        $addedCampaign = $result->getValue();
        $addedCampaignId = $addedCampaign[0]->getId();
        $campaignArray['google_campaign_id'] = $addedCampaignId;
        $campaignArray['campaign_response'] = json_encode($addedCampaign);
        \App\GoogleAdsCampaign::whereId($campaignDetail->id)->update($campaignArray);  
        //return redirect()->route('googlecampaigns.index');
        return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign updated successfully');;
    }

    // delete campaign
    public function deleteCampaign(Request $request, $campaignId)
    {
        $account_id=$request->delete_account_id;
        $storagepath = $this->getstoragepath($account_id);
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile($storagepath)->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile($storagepath)->withOAuth2Credential($oAuth2Credential)->build();

        $adWordsServices = new AdWordsServices();

        $campaignService = $adWordsServices->get($session, CampaignService::class);

        $operations = [];
        // Create a campaign with REMOVED status.
        $campaign = new Campaign();
        $campaign->setId($campaignId);
        $campaign->setStatus(CampaignStatus::REMOVED);

        // Create a campaign operation and add it to the list.
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Remove the campaign on the server.
        $result = $campaignService->mutate($operations);
        //delete from database
        \App\GoogleAdsCampaign::where('account_id',$account_id)->where('google_campaign_id',$campaignId)->delete();
        /* return redirect()->route('googlecampaigns.index'); */
        return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign deleted successfully');;
    }
}
