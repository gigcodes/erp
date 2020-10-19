<?php

namespace App\Http\Controllers;

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\Ad;
use Google\AdsApi\AdWords\v201809\cm\AdGroup;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAd;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdStatus;
use Google\AdsApi\AdWords\v201809\cm\AdType;
use Google\AdsApi\AdWords\v201809\cm\ExpandedTextAd;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Illuminate\Http\Request;

class GoogleAdsController extends Controller
{
    const PAGE_LIMIT = 500;

    public function index(Request $request, $campaignId, $adGroupId) {
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adsInfo = $this->getAds(new AdWordsServices(), $session, $adGroupId);

        return view('googleads.index',
            ['ads' => $adsInfo['ads'], 'totalNumEntries' => $adsInfo['totalNumEntries'],
                'campaignId' => $campaignId, 'adGroupId' => $adGroupId]);
    }

    // getting ads
    public function getAds(AdWordsServices $adWordsServices, AdWordsSession $session, $adGroupId) {
        $adGroupAdService = $adWordsServices->get($session, AdGroupAdService::class);

        // Create a selector to select all ads for the specified ad group.
        $selector = new Selector();
        $selector->setFields(
            ['Id', 'Status', 'HeadlinePart1', 'HeadlinePart2', 'Description']
        );
        $selector->setOrdering([new OrderBy('Id', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [
                new Predicate('AdGroupId', PredicateOperator::IN, [$adGroupId]),
                new Predicate(
                    'AdType',
                    PredicateOperator::IN,
                    [AdType::EXPANDED_TEXT_AD]
                ),
                new Predicate(
                    'Status',
                    PredicateOperator::IN,
                    [AdGroupAdStatus::ENABLED, AdGroupAdStatus::PAUSED]
                )
            ]
        );

        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $totalNumEntries = 0;
        $ads = [];
        do {
            // Retrieve ad group ads one page at a time, continuing to request pages
            // until all ad group ads have been retrieved.
            $page = $adGroupAdService->get($selector);

            // Print out some information for each ad group ad.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $adGroupAd) {
                    $ad = $adGroupAd->getAd();
                    $ads[] = [
                        'adId' => $ad->getId(),
                        'status' => $adGroupAd->getStatus(),
                        'headlinePart1' => $ad->getHeadlinePart1(),
                        'headlinePart2' => $ad->getHeadlinePart2(),
                        'description' => $ad->getDescription(),
                        'type' => $ad->getAdType()
                    ];
                }
            }

            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'ads' => $ads,
            'totalNumEntries' => $totalNumEntries
        ];
    }

    // go to ad create page
    public function createPage($campaignId, $adGroupId) {
        //
        return view('googleads.create', ['campaignId' => $campaignId, 'adGroupId' => $adGroupId]);
    }

    // create ad
    public function createAd(Request $request, $campaignId, $adGroupId) {
        $adStatuses = ['ENABLED', 'PAUSED', 'DISABLED'];
        $headlinePart1 = $request->headlinePart1;
        $headlinePart2 = $request->headlinePart2;
        $headlinePart3 = $request->headlinePart3;
        $description1 = $request->description1;
        $description2 = $request->description2;
        $finalUrl = $request->finalUrl;
        $path1 = $request->path1;
        $path2 = $request->path2;
        $adStatus = $adStatuses[$request->adStatus];

        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroupAdService = (new AdWordsServices())->get($session, AdGroupAdService::class);

        $operations = [];
        // Create an expanded text ad.
        $expandedTextAd = new ExpandedTextAd();
        $expandedTextAd->setHeadlinePart1($headlinePart1);
        $expandedTextAd->setHeadlinePart2($headlinePart2);
        $expandedTextAd->setHeadlinePart3($headlinePart3);
        $expandedTextAd->setDescription($description1);
        $expandedTextAd->setDescription2($description2);
        $expandedTextAd->setFinalUrls([$finalUrl]);
        $expandedTextAd->setPath1($path1);
        $expandedTextAd->setPath2($path2);

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($adGroupId);
        $adGroupAd->setAd($expandedTextAd);

        // Optional: Set additional settings.
        $adGroupAd->setStatus($adStatus);

        // Create ad group ad operation and add it to the list.
        $operation = new AdGroupAdOperation();
        $operation->setOperand($adGroupAd);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;

        // Add expanded text ads on the server.
        $result = $adGroupAdService->mutate($operations);

        return redirect('googlecampaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/ads');
    }

    // go to ad update page
    public function updatePage() {

    }

    // update ad
    public function updateAd() {

    }

    // delete ad
    public function deleteAd(Request $request, $campaignId, $adGroupId, $adId) {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroupAdService = (new AdWordsServices())->get($session, AdGroupAdService::class);

        $operations = [];
        // Create ad using an existing ID. Use the base class Ad instead of TextAd
        // to avoid having to set ad-specific fields.
        $ad = new Ad();
        $ad->setId($adId);

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->setAdGroupId($adGroupId);
        $adGroupAd->setAd($ad);

        // Create ad group ad operation and add it to the list.
        $operation = new AdGroupAdOperation();
        $operation->setOperand($adGroupAd);
        $operation->setOperator(Operator::REMOVE);
        $operations[] = $operation;

        // Remove the ad on the server.
        $result = $adGroupAdService->mutate($operations);

        return redirect('googlecampaigns/' . $campaignId . '/adgroups/' . $adGroupId . '/ads');
    }
}
