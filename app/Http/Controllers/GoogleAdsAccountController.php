<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use App\GoogleAd;
use App\GoogleAdsGroup;
use Google\Auth\OAuth2;
use App\GoogleAdsCampaign;
use App\Models\GoogleAppAd;
use Illuminate\Http\Request;
use App\Helpers\GoogleAdsHelper;
use App\Models\GoogleAppAdImage;
use Google\Auth\CredentialsLoader;
use App\Models\GoogleAdGroupKeyword;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleCampaignTargetLanguage;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use App\Models\GoogleResponsiveDisplayAdMarketingImage;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;

class GoogleAdsAccountController extends Controller
{
    // show campaigns in main page
    public function index(Request $request)
    {
        $query = \App\GoogleAdsAccount::query();
        if ($request->website) {
            $query = $query->where('store_websites', $request->website);
        }
        if ($request->accountname) {
            $query = $query->where('account_name', 'LIKE', '%' . $request->accountname . '%');
        }

        $googleadsaccount = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleadsaccounts.partials.list-adsaccount', compact('googleadsaccount'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $googleadsaccount->render(),
                'count' => $googleadsaccount->total(),
            ], 200);
        }

        $store_website = \App\StoreWebsite::all();
        $totalentries = $googleadsaccount->count();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Google AdWords Account',
            'message' => 'Viewed account listing',
        ];
        insertGoogleAdsLog($input);

        return view('googleadsaccounts.index', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries, 'store_website' => $store_website]);
    }

    public function createGoogleAdsAccountPage()
    {
        $store_website = \App\StoreWebsite::all();

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Google AdWords Account',
            'message' => 'Viewed create account',
        ];
        insertGoogleAdsLog($input);

        return view('googleadsaccounts.create', ['store_website' => $store_website]);
    }

    public function createGoogleAdsAccount(Request $request)
    {
        //create account
        $this->validate($request, [
            'google_customer_id' => 'required|integer',
            'account_name' => 'required',
            'store_websites' => 'required',
            'status' => 'required',
            'notes' => 'required',
            'google_adwords_client_account_email' => 'required|email',
            'google_adwords_client_account_password' => 'required',
            'google_adwords_manager_account_customer_id' => 'required|integer',
            'google_adwords_manager_account_developer_token' => 'required',
            'google_adwords_manager_account_email' => 'required|email',
            'google_adwords_manager_account_password' => 'required',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
            'google_map_api_key' => 'required',
            'google_merchant_center_account_id' => 'required|integer',
        ]);

        try {
            $input = $request->all();
            $googleadsAc = \App\GoogleAdsAccount::create($input);
            $account_id = $googleadsAc->id;

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Google AdWords Account',
                'message' => 'Created new account',
                'response' => json_encode($googleadsAc),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('/google-campaigns/ads-account')->with('actSuccess', 'GoogleAdwords account details added successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Google AdWords Account',
                'message' => 'Create new account > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('/google-campaigns/ads-account')->with('actError', $e->getMessage());
        }
    }

    public function editeGoogleAdsAccountPage($id)
    {
        $store_website = \App\StoreWebsite::all();
        $googleAdsAc = \App\GoogleAdsAccount::findOrFail($id);

        // Insert google ads log
        $input = [
            'type' => 'SUCCESS',
            'module' => 'Google AdWords Account',
            'message' => 'Viewed update account of ' . $googleAdsAc->account_name,
        ];
        insertGoogleAdsLog($input);

        return $googleAdsAc;
    }

    public function updateGoogleAdsAccount(Request $request)
    {
        $account_id = $request->account_id;
        //update account
        $this->validate($request, [
            'google_customer_id' => 'required|integer',
            'account_name' => 'required',
            'store_websites' => 'required',
            'status' => 'required',
            'google_adwords_client_account_email' => 'required|email',
            'google_adwords_client_account_password' => 'required',
            'google_adwords_manager_account_customer_id' => 'required|integer',
            'google_adwords_manager_account_developer_token' => 'required',
            'google_adwords_manager_account_email' => 'required|email',
            'google_adwords_manager_account_password' => 'required',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
            'google_map_api_key' => 'required',
            'google_merchant_center_account_id' => 'required|integer',
        ]);

        try {
            $input = $request->all();
            $googleadsAcQuery = new \App\GoogleAdsAccount;
            $googleadsAc = $googleadsAcQuery->find($account_id);

            $googleadsAc->fill($input);
            $googleadsAc->save();

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Google AdWords Account',
                'message' => 'Updated account details for ' . $googleadsAc->account_name,
                'response' => json_encode($googleadsAc),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('/google-campaigns/ads-account')->with('actSuccess', 'GoogleAdwords account details updated successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Google AdWords Account',
                'message' => 'Update account > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('/google-campaigns/ads-account')->with('actError', $e->getMessage());
        }
    }

    /*
    * used to get google refresh token for ads
    */
    public function refreshToken(Request $request)
    {
        $google_redirect_url = route('googleadsaccount.get-refresh-token');

        $PRODUCTS = [
            ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
            ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
            ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE') . ' '
                . config('google.GOOGLE_ADS_MANAGER_API_SCOPE'), ],
        ];

        $client_id = $request->client_id;
        $client_secret = $request->client_secret;
        Session::put('client_id', $client_id);
        Session::put('client_secret', $client_secret);
        Session::save();

        $api = intval(2);

        $scopes = $PRODUCTS[$api][1];

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $client_id,
                'clientSecret' => $client_secret,
                'scope' => $scopes,
            ]
        );

        $authUrl = $oauth2->buildFullAuthorizationUri([
            'prompt' => 'consent',
        ]);

        $authUrl = filter_var($authUrl, FILTER_SANITIZE_URL);

        return redirect()->away($authUrl);
    }

    /*
    * Refresh token Redirect API
    */
    public function getRefreshToken(Request $request)
    {
        $google_redirect_url = route('googleadsaccount.get-refresh-token');
        $api = intval(2);
        $PRODUCTS = [
            ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
            ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
            ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE') . ' '
                . config('google.GOOGLE_ADS_MANAGER_API_SCOPE'), ],
        ];
        $scopes = $PRODUCTS[$api][1];
        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => Session::get('client_id'),
                'clientSecret' => Session::get('client_secret'),
                'scope' => $scopes,
            ]
        );
        if ($request->code) {
            $code = $request->code;
            $oauth2->setCode($code);
            $authToken = $oauth2->fetchAuthToken();
            Session::forget('client_secret');
            Session::forget('client_id');

            return view('googleadsaccounts.view_token', ['refresh_token' => $authToken['refresh_token'], 'access_token' => $authToken['access_token']]);
        } else {
            return redirect('/google-campaigns/ads-account')->with('message', 'Unable to Get Tokens ');
        }
    }

    public function getstoragepath($account_id)
    {
        $result = \App\GoogleAdsAccount::find($account_id);
        if (isset($result->config_file_path) && $result->config_file_path != '' && \Storage::disk('adsapi')->exists($account_id . '/' . $result->config_file_path)) {
            $storagepath = \Storage::disk('adsapi')->url($account_id . '/' . $result->config_file_path);
            $storagepath = storage_path('app/adsapi/' . $account_id . '/' . $result->config_file_path);

            return $storagepath;
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
    }

    public function deleteGoogleAdsAccount($id)
    {
        $googleAdsAc = \App\GoogleAdsAccount::findOrFail($id);

        try {
            $account_id = $id;
            $customerId = $googleAdsAc->google_customer_id;

            $googleAdsCampaigns = GoogleAdsCampaign::where('account_id', $account_id)->get();

            foreach ($googleAdsCampaigns as $campaign) {
                $campaignId = $campaign->google_campaign_id;

                try {
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
                } catch (Exception $e) {
                }

                // Delete other data
                GoogleAdGroupKeyword::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleResponsiveDisplayAd::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleResponsiveDisplayAdMarketingImage::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleAppAd::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleAppAdImage::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleAd::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleAdsGroup::where('adgroup_google_campaign_id', $campaignId)->delete();
                GoogleCampaignTargetLanguage::where('adgroup_google_campaign_id', $campaignId)->delete();

                $campaign->delete();
            }

            // Insert google ads log
            $input = [
                'type' => 'SUCCESS',
                'module' => 'Google AdWords Account',
                'message' => 'Deleted google adwords account of ' . $googleAdsAc->account_name,
            ];

            insertGoogleAdsLog($input);

            $googleAdsAc->delete();

            return redirect()->to('/google-campaigns/ads-account')->with('actSuccess', 'GoogleAdwords account deleted successfully');
        } catch (Exception $e) {
            // Insert google ads log
            $input = [
                'type' => 'ERROR',
                'module' => 'Google AdWords Account',
                'message' => 'Deleted google adwords account > ' . $e->getMessage(),
            ];
            insertGoogleAdsLog($input);

            return redirect()->to('/google-campaigns/ads-account')->with('actError', $e->getMessage());
        }
    }
}
