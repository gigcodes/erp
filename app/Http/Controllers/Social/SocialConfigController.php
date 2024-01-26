<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialConfig\EditRequest;
use App\Http\Requests\SocialConfig\StoreRequest;
use App\Language;
use App\LogRequest;
use App\Setting;
use App\Social\SocialConfig;
use App\StoreWebsite;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array|Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $query = SocialConfig::query();

        // Refactor the condition to make it more readable and efficient
        if ($this->shouldApplyBasicFilter($request)) {
            // No additional conditions are applied
        } else {
            // Apply filters based on the request
            $this->applyAdvancedFilters($query, $request);
        }

        $socialConfigs = $query->orderBy('id', 'desc')->paginate(Setting::get('pagination'));

        // Load additional data only if it's not an AJAX request
        if (!$request->ajax()) {
            $additionalData = $this->getAdditionalData($request);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.configs.partials.data', compact('socialConfigs'))->render(),
                'links' => (string)$socialConfigs->links(),
            ]);
        }

        return view('social.configs.index', array_merge(compact('socialConfigs'), $additionalData ?? []));
    }

    protected function shouldApplyBasicFilter(Request $request)
    {
        return $request->number || $request->username || $request->provider ||
            ($request->customer_support || $request->term || $request->date) &&
            $request->customer_support == 0;
    }

    protected function applyAdvancedFilters($query, Request $request)
    {
        if ($request->store_website_id) {
            $query->whereIn('store_website_id', $request->store_website_id);
        }

        if ($request->user_name) {
            $query->whereIn('email', $request->user_name);
        }

        if ($request->platform) {
            $query->whereIn('platform', $request->platform);
        }
    }

    protected function getAdditionalData(Request $request)
    {
        $socialConfig = SocialConfig::select(['email', 'platform'])->distinct()->get();

        return [
            'websites' => StoreWebsite::select('id', 'title')->get(),
            'user_names' => SocialConfig::select('email')->distinct()->get(),
            'platforms' => SocialConfig::select('platform')->distinct()->get(),
            'languages' => Language::get(),
            'selected_website' => $request->store_website_id,
            'selected_user_name' => $request->user_name,
            'selected_platform' => $request->platform,
        ];
    }

    public function getadsAccountManager(Request $request)
    {
        $user_access_token = $request['token'];
        $fields = 'account_id,name,currency,balance,account_status,business_name,business_id';

        $url = 'https://graph.facebook.com/v15.0/me/adaccounts?fields=' . $fields;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $user_access_token,
        ]);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, SocialConfigController::class, 'getadsAccountManager');

        $data = json_decode($response, true);

        return $data['data'];
    }

    //@todo need to confirm is this being used anywhere
    public function getfbToken()
    {
        return redirect('https://www.facebook.com/dialog/oauth?client_id=1465672917171155&redirect_uri=https://example.com&scope=manage_pages,pages_manage_posts');
        $curl = curl_init();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = sprintf('https://www.facebook.com/dialog/oauth?client_id=1465672917171155&redirect_uri=https://example.com&scope=manage_pages,pages_manage_posts');
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = json_decode(curl_exec($curl), true); //response decoded
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $httpcode, SocialConfigController::class, 'getfbToken');
    }

    /**
     * Method to generate the Facebook access token and get
     * the basic profile details about the account.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function getfbTokenBack(Request $request)
    {
        $code = $request['code'];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $accessTokenUrl = 'https://graph.facebook.com/v15.0/oauth/access_token?client_id=' .
            config('facebook.config.app_id') . '&redirect_uri=' . route('social.config.fbtokenback') .
            '&client_secret=' . config('facebook.config.app_secret') . '&code=' . $code;

        $http = Http::get($accessTokenUrl);
        $response = $http->json();

        LogRequest::log(
            $startTime, $accessTokenUrl, 'GET',
            json_encode([]), $response, $http->status(),
            SocialConfigController::class,
            'getfbTokenBack'
        );

        $meUrl = 'https://graph.facebook.com/v15.0/me/?access_token=' . $response['access_token'];
        $meHttp = Http::get($meUrl);
        $meResponse = $meHttp->json();

        LogRequest::log($startTime, $meUrl, 'GET', json_encode([]), $meResponse, $meHttp->status(), SocialConfigController::class, 'getfbTokenBack');

        SocialConfig::create([
            'account_id' => $meResponse['id'],
            'name' => $meResponse['name'],
            'token' => $response['access_token']
        ]);

        return redirect()->route('social.config.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $pageId = $request->page_id;
        $data = $request->validated();
        $data['page_language'] = $request->page_language;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        if ($request->platform == 'instagram') {
            $curl = curl_init();

            $url = sprintf('https://graph.facebook.com/v15.0/' . $request->page_id . '?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->page_token);

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true); //response deocded
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $httpcode, SocialConfigController::class, 'store');

            if ($id = $response['instagram_business_account']['id']) {
                $data['account_id'] = $id;
            } else {
                return redirect()->back()->withError('Page Linked Account ID not found.');
            }
        } else {
            $data['account_id'] = $pageId;
        }

        SocialConfig::create($data);
        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(EditRequest $request)
    {
        $pageId = $request->page_id;
        $config = SocialConfig::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        if (isset($request->adsmanager)) {
            $data['ads_manager'] = $request->adsmanager;
        }

        if ($request->platform == 'instagram') {
            $curl = curl_init();
            $url = sprintf('https://graph.facebook.com/v16.0/' . $request->page_id . '?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->token);
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true); //response deocded
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $httpcode, SocialConfigController::class, 'edit');

            if ($id = $response['instagram_business_account']['id']) {
                $data['account_id'] = $id;
            } else {
                return redirect()->back()->withError('Page Linked Account ID not found.');
            }
        } else {
            $data['account_id'] = $pageId;
        }
        $data['page_language'] = $request->page_language;
        $config->fill($data);
        $config->save();
        // $config->update($data);

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $config = SocialConfig::findorfail($request->id);
        $config->delete();
        return response()->jsonResponse(message: 'Config Deleted');
    }
}
