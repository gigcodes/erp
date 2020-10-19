<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\AdGroupService;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdWords\v201809\cm\AdGroup;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdRotationMode;
use Google\AdsApi\AdWords\v201809\cm\AdGroupOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroupStatus;
use Google\AdsApi\AdWords\v201809\cm\AdRotationMode;
use Google\AdsApi\AdWords\v201809\cm\BiddingStrategyConfiguration;
use Google\AdsApi\AdWords\v201809\cm\CpcBid;
use Google\AdsApi\AdWords\v201809\cm\CriterionTypeGroup;
use Google\AdsApi\AdWords\v201809\cm\Money;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\TargetingSetting;
use Google\AdsApi\AdWords\v201809\cm\TargetingSettingDetail;

class GoogleAdGroupController extends Controller
{
    const PAGE_LIMIT = 500;
    const CPC_BID_MICRO_AMOUNT = null;

    public function index(Request $request, $campaignId) {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroups = $this->getAdGroups(new AdWordsServices(), $session, $campaignId);

        return view('googleadgroups.index', ['adGroups' => $adGroups['adGroups'], 'totalNumEntries' => $adGroups['totalNumEntries'], 'campaignId' => $campaignId]);
    }

    // getting all Ad Groups of specific campaign
    public function getAdGroups(AdWordsServices $adWordsServices, AdWordsSession $session, $campaignId) {
        $adGroupService = $adWordsServices->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        $selector = new Selector();
        $selector->setFields(['Id', 'Name', 'Status', 'CpcBid']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [new Predicate('CampaignId', PredicateOperator::IN, [$campaignId])]
        );
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $totalNumEntries = 0;
        $adGroups = [];
        do {
            // Retrieve ad groups one page at a time, continuing to request pages
            // until all ad groups have been retrieved.
            $page = $adGroupService->get($selector);

            // Print out some information for each ad group.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $adGroup) {
                    $adGroups[] = [
                        'adGroupId' => $adGroup->getId(),
                        'name' => $adGroup->getName(),
                        'status' => $adGroup->getStatus(),
                        'bidAmount' => $adGroup->getBiddingStrategyConfiguration()->getBids()[0]->getBid()->getMicroAmount() / 1000000
                    ];
                }
            }

            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'totalNumEntries' => $totalNumEntries,
            'adGroups' => $adGroups
        ];
    }

    // got to ad group create page
    public function createPage($campaignId) {
        //
        return view('googleadgroups.create', ['campaignId' => $campaignId]);
    }

    // create ad group
    public function createAdGroup(Request $request, $campaignId) {
        $adGroupStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
//        $criterionTypeGroups = ['KEYWORD', 'USER_INTEREST_AND_LIST', 'VERTICAL', 'GENDER', 'AGE_RANGE', 'PLACEMENT', 'PARENT', 'INCOME_RANGE', 'NONE', 'UNKNOWN'];
//        $adRotationModes = ['UNKNOWN', 'OPTIMIZE', 'ROTATE_FOREVER'];
        $adGroupName = $request->adGroupName;
        $microAmount = $request->microAmount * 1000000;
        $adGroupStatus = $adGroupStatusArr[$request->adGroupStatus];
//        $criterionTypeGroup = $criterionTypeGroups[$request->criterionTypeGroup];
//        $adRotationMode = $adRotationModes[$request->adRotationMode];

        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroupService = (new AdWordsServices())->get($session, AdGroupService::class);

        $operations = [];

        /// Create an ad group with required settings and specified status.
        $adGroup = new AdGroup();
        $adGroup->setCampaignId($campaignId);
        $adGroup->setName($adGroupName);

        // Set bids (required).
        $bid = new CpcBid();
        $money = new Money();
        $money->setMicroAmount($microAmount);
        $bid->setBid($money);
        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $biddingStrategyConfiguration->setBids([$bid]);
        $adGroup->setBiddingStrategyConfiguration($biddingStrategyConfiguration);

        $adGroup->setStatus($adGroupStatus);

        // Create an ad group operation and add it to the operations list.
        $operation = new AdGroupOperation();
        $operation->setOperand($adGroup);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;


        // Create the ad groups on the server
        $result = $adGroupService->mutate($operations);

        return redirect('googlecampaigns/' . $campaignId . '/adgroups');
    }

    // go to update page
    public function updatePage(Request $request, $campaignId, $adGroupId) {
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroupService = (new AdWordsServices())->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        $selector = new Selector();
        $selector->setFields(['Id', 'Name', 'Status', 'CampaignId', 'CampaignName', 'CpcBid']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [new Predicate('CampaignId', PredicateOperator::IN, [$campaignId]),
             new Predicate('Id', PredicateOperator::IN, [$adGroupId])]
        );
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $page = $adGroupService->get($selector);
        $pageEntries = $page->getEntries();

        $adGroup = [];
        if ($pageEntries !== null) {
            $adGroup = $pageEntries[0];
        }

        $adGroup = [
            'adGroupId' => $adGroup->getId(),
            'name' => $adGroup->getName(),
            'status' => $adGroup->getStatus(),
            'bidAmount' => $adGroup->getBiddingStrategyConfiguration()->getBids()[0]->getBid()->getMicroAmount() / 1000000
        ];

        return view('googleadgroups.update', ['adGroup' => $adGroup, 'campaignId' => $campaignId]);
    }

    // update ad group
    public function updateAdGroup(Request $request, $campaignId) {
        $adGroupStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
        $adGroupId = $request->adGroupId;
        $adGroupName = $request->adGroupName;
        $cpcBidMicroAmount = $request->cpcBidMicroAmount * 1000000;
        $adGroupStatus = $adGroupStatusArr[$request->adGroupStatus];

        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroupService = (new AdWordsServices())->get($session, AdGroupService::class);

        $operations = [];
        // Create ad group with the specified ID.
        $adGroup = new AdGroup();
        $adGroup->setId($adGroupId);
        $adGroup->setName($adGroupName);
        $adGroup->setStatus($adGroupStatus);

        // Update the CPC bid if specified.
        if (!is_null($cpcBidMicroAmount)) {
            $bid = new CpcBid();
            $money = new Money();
            $money->setMicroAmount($cpcBidMicroAmount);
            $bid->setBid($money);
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->setBids([$bid]);
            $adGroup->setBiddingStrategyConfiguration($biddingStrategyConfiguration);
        }

        // Create ad group operation and add it to the list.
        $operation = new AdGroupOperation();
        $operation->setOperand($adGroup);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Update the ad group on the server.
        $result = $adGroupService->mutate($operations);

        return redirect('googlecampaigns/' . $campaignId . '/adgroups');
    }

    // delete ad group
    public function deleteAdGroup(Request $request, $campaignId, $adGroupId) {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adWordsServices = new AdWordsServices();

        $adGroupService = $adWordsServices->get($session, AdGroupService::class);

        $operations = [];
        // Create ad group with REMOVED status.
        $adGroup = new AdGroup();
        $adGroup->setId($adGroupId);
        $adGroup->setStatus(AdGroupStatus::REMOVED);

        // Create ad group operation and add it to the list.
        $operation = new AdGroupOperation();
        $operation->setOperand($adGroup);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Remove the ad group on the server.
        $result = $adGroupService->mutate($operations);

        $adGroup = $result->getValue()[0];

        return redirect('googlecampaigns/' . $campaignId . '/adgroups');
    }
}
