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


class GoogleAdsController extends Controller
{
    // show campaigns in googleads main page
    public function index() {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile()
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campInfo = $this->getCampaigns($adWordsServices, $session);

        return view('googleads.index', ['campaigns' => $campInfo['campaigns'], 'totalNumEntries' => $campInfo['totalNumEntries']]);
    }

    // get campaigns and total count
    public function getCampaigns(AdWordsServices $adWordsServices, AdWordsSession $session) {
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
    public function createPage() {
        //
        return view('googleads.create');
    }

    // create campaign
    public function createCampaign(Request $request) {
        $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
        $budgetAmount = $request->budgetAmount * 1000000;
        $campaignName = $request->campaignName;
        $campaignStatus = $campaignStatusArr[$request->campaignStatus];

        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile()
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $budgetService = $adWordsServices->get($session, BudgetService::class);

        // Create the shared budget (required).
        $budget = new Budget();
        $budget->setName('Interplanetary Cruise Budget #' . uniqid());

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
        $campaign->setStartDate(date('Ymd', strtotime('+1 day')));
        $campaign->setEndDate(date('Ymd', strtotime('+1 month')));

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

        return redirect()->route('googleads.index');
    }

    // go to update page
    public function updatePage(Request $request, $campaignId) {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile()
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile()
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
        //
        return view('googleads.update', ['campaign' => $campaign]);
    }

    // save campaign's changes
    public function updateCampaign(Request $request) {
        $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
        $campaignId = $request->campaignId;
        $campaignName = $request->campaignName;
        $campaignStatus = $campaignStatusArr[$request->campaignStatus];

        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile()
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campaignService = $adWordsServices->get($session, CampaignService::class);

        $operations = [];
        // Create a campaign with ... status.
        $campaign = new Campaign();
        $campaign->setId($campaignId);
        $campaign->setName($campaignName);
        $campaign->setStatus($campaignStatus);

        // Create a campaign operation and add it to the list.
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Update the campaign on the server.
        $result = $campaignService->mutate($operations);

        return redirect()->route('googleads.index');
    }

    // delete campaign
    public function deleteCampaign(Request $request, $campaignId) {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile()->withOAuth2Credential($oAuth2Credential)->build();

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

        return redirect()->route('googleads.index');
    }
}
