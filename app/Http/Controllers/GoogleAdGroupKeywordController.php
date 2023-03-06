<?php

namespace App\Http\Controllers;

use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V12\Services\AdGroupCriterionOperation;

use Illuminate\Http\Request;
use App\Models\GoogleAdGroupKeyword;
use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsGroup;
use Storage;

class GoogleAdGroupKeywordController extends Controller
{
    const PAGE_LIMIT = 500;

    public $exceptionError = 'Something went wrong';

    // show campaigns in main page
    public function getstoragepath($account_id)
    {
        $result = GoogleAdsAccount::find($account_id);
        if (\Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = Storage::disk('adsapi')->url($account_id.'/'.$result->config_file_path);
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);
            
            return $storagepath;
        } else {
            abort(404, 'Please add adspai_php.ini file');
        }
    }

    public function getAccountDetail($campaignId)
    {
        $campaignDetail = GoogleAdsCampaign::where('google_campaign_id', $campaignId)->first();
        if ($campaignDetail->exists() > 0) {
            return [
                'account_id' => $campaignDetail->account_id,
                'campaign_name' => $campaignDetail->campaign_name,
                'google_customer_id' => $campaignDetail->google_customer_id,
            ];
        } else {
            abort(404, 'Invalid account!');
        }
    }

    public function index(Request $request, $campaignId, $adGroupId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $campaign_account_id = $acDetail['account_id'];

        $where = array(
                    'google_adgroup_id' => $adGroupId,
                    'adgroup_google_campaign_id' => $campaignId,
                );

        $adGroup = GoogleAdsGroup::where($where)->firstOrFail();
        $ad_group_name = $adGroup->ad_group_name;

        $keywords = GoogleAdGroupKeyword::where($where);

        if ($request->keyword) {
            $keywords = $keywords->where('keyword', 'LIKE', '%'.$request->keyword.'%');
        }

        $keywords = $keywords->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google_ad_group_keyword.partials.list', ['keywords' => $keywords, 'campaignId' => $campaignId, 'adGroupId' => $adGroupId])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $keywords->render(),
                'count' => $keywords->total(),
            ], 200);
        }

        $totalEntries = $keywords->total();

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Ad Group keyword',
                    'message' => "Viewed ad group keyword listing for ". $ad_group_name
                );
        insertGoogleAdsLog($input);

        return view('google_ad_group_keyword.index', ['keywords' => $keywords, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'ad_group_name' => $ad_group_name, 'campaign_account_id' => $campaign_account_id, 'adGroupId' => $adGroupId]);
    }

    // delete keyword
    public function deleteKeyword(Request $request, $campaignId, $adGroupId, $keywordId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $customerId = $acDetail['google_customer_id'];

        $storagepath = $this->getstoragepath($account_id);

        $where = array(
                    'google_adgroup_id' => $adGroupId,
                    'adgroup_google_campaign_id' => $campaignId,
                    'google_keyword_id' => $keywordId,
                );

        $keyword = GoogleAdGroupKeyword::where($where)->firstOrFail();

        try {
            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            // Creates ad group criterion resource name.
            $adGroupCriterionResourceName =
                ResourceNames::forAdGroupCriterion($customerId, $adGroupId, $keywordId);

            // Constructs an operation that will remove the keyword with the specified resource name.
            $adGroupCriterionOperation = new AdGroupCriterionOperation();
            $adGroupCriterionOperation->setRemove($adGroupCriterionResourceName);

            // Issues a mutate request to remove the ad group criterion.
            $adGroupCriterionServiceClient = $googleAdsClient->getAdGroupCriterionServiceClient();
            $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
                $customerId,
                [$adGroupCriterionOperation]
            );

            $removedAdGroupCriterion = $response->getResults()[0];

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Ad Group keyword',
                        'message' => "Deleted ad group keyword for ". $keyword->ad_group->ad_group_name,
                        'response' => json_encode($keyword)
                    );

            $keyword->delete();

            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword')->with('actSuccess', 'Ad group keyword deleted successfully');
        } catch (Exception $e) {

            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Ad Group keyword',
                        'message' => 'Delete ad group keyword > ' . $e->getMessage(),
                    );
            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups/'.$adGroupId.'/ad-group-keyword')->with('actError', $this->exceptionError);
        }
    }
}
