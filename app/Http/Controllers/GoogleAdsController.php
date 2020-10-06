<?php

namespace App\Http\Controllers;

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\BudgetService;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\Common\Util\EnvironmentalVariables;
use Google\AdsApi\GetCampaigns;
use Illuminate\Http\Request;
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


class GoogleAdsController extends Controller
{
    public function index(Request $request, EnvironmentalVariables $environmentalVariables) {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile()
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();



//        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector
//        $selector = new Selector();
//        $selector->setFields(array('Id', 'Name'));
//        $selector->setOrdering(array(new OrderBy('Name', 'ASCENDING')));

        // Create paging controls
//        $selector->setPaging(new Paging(0, 100));
        // Make the get request
//        $page = $campaignService->get($selector);

        $campCount = $this->getCampaignsCount($adWordsServices, $session);

//        $this->createCampaign($adWordsServices, $session);

        return view('googleads.index', ['campaignCount' => $campCount]);
    }

    public function create(Request $request) {
        // creating ads
        return view('googleads.create');
    }

    public function createCampaign(AdWordsServices $adWordsServices, AdWordsSession $session) {
        $budgetService = $adWordsServices->get($session, BudgetService::class);

        // Create the shared budget (required).
        $budget = new Budget();
        $budget->setName('Interplanetary Cruise Budget #' . uniqid());

        $money = new Money();
        $money->setMicroAmount(50000000);
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
        $campaign->setName('Interplanetary Cruise #' . uniqid());
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
        $campaign->setStatus(CampaignStatus::PAUSED);
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

        // Create a campaign with only required settings.
        $campaign = new Campaign();
        $campaign->setName('Interplanetary Cruise #' . uniqid());
        $campaign->setAdvertisingChannelType(AdvertisingChannelType::DISPLAY);

        // Set shared budget (required).
        $campaign->setBudget(new Budget());
        $campaign->getBudget()->setBudgetId($budget->getBudgetId());

        // Set bidding strategy (required).
        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $biddingStrategyConfiguration->setBiddingStrategyType(
            BiddingStrategyType::MANUAL_CPC
        );
        $campaign->setBiddingStrategyConfiguration($biddingStrategyConfiguration);

        $campaign->setStatus(CampaignStatus::PAUSED);

        // Create a campaign operation and add it to the operations list.
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;

        // Create the campaigns on the server and print out some information for
        // each created campaign.
        $result = $campaignService->mutate($operations);
        foreach ($result->getValue() as $campaign) {
            printf(
                "Campaign with name '%s' and ID %d was added.\n",
                $campaign->getName(),
                $campaign->getId()
            );
        }
    }

    function getCampaignsCount(AdWordsServices $adWordsServices, AdWordsSession $session) {
        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector.
        $selector = new Selector();
        $selector->setFields(['Id', 'Name']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPaging(new Paging(0, 10));

        $totalNumEntries = 0;
        do {
            // Make the get request.
            $page = $campaignService->get($selector);

            // Display results.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $campaign) {
                    printf(
                        "Campaign with ID %d and name '%s' was found.\n",
                        $campaign->getId(),
                        $campaign->getName()
                    );
                }
            }

            // Advance the paging index.
            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + 10
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

//        printf("Number of results found: %d\n", $totalNumEntries);
        return $totalNumEntries;
    }
}
