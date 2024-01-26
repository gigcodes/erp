<?php

namespace App\Http\Controllers\Social;

use App\Setting;
use App\Language;
use App\LogRequest;
use App\StoreWebsite;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\SocialConfig\EditRequest;
use App\Http\Requests\SocialConfig\StoreRequest;
use Illuminate\Contracts\Foundation\Application;

class SocialConfigController extends Controller
{
    protected string $fb_base_url;

    public function __construct()
    {
        $this->fb_base_url = 'https://graph.facebook.com/' . config('facebook.config.default_graph_version') . '/';
    }

    /**
     * Social config page results
     *
     * @return array|Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $query = SocialConfig::query();

        if ($this->shouldApplyBasicFilter($request)) {
            // No additional conditions are applied
        } else {
            // Apply filters based on the request
            $this->applyAdvancedFilters($query, $request);
        }

        $socialConfigs = $query->orderBy('id', 'desc')->paginate(Setting::get('pagination'));

        if (! $request->ajax()) {
            $additionalData = $this->getAdditionalData($request);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.configs.partials.data', compact('socialConfigs'))->render(),
                'links' => (string) $socialConfigs->links(),
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

    /**
     * Data that is sent to the index blade on all the conditions
     *
     * @return array
     */
    protected function getAdditionalData(Request $request)
    {
        return [
            'facebook_url' => 'https://www.facebook.com/dialog/oauth?client_id=' . config('facebook.config.app_id') .
                '&redirect_uri=' . config('app.url') .
                '/social/config/fbtokenback&scope=instagram_basic,instagram_manage_insights,instagram_content_publish,instagram_manage_comments,instagram_manage_messages,pages_manage_posts,pages_show_list',
            'websites' => StoreWebsite::select('id', 'title')->get(),
            'user_names' => SocialConfig::select('email')->distinct()->get(),
            'platforms' => SocialConfig::select('platform')->distinct()->get(),
            'languages' => Language::get(),
            'selected_website' => $request->store_website_id,
            'selected_user_name' => $request->user_name,
            'selected_platform' => $request->platform,
        ];
    }

    //@todo on validation of the request it is redirecting the page multiple times. Need to validate what is happening.
    public function getadsAccountManager(Request $request)
    {
        $user_access_token = $request['token'];
        $fields = 'account_id,name,currency,balance,account_status,business_name,business_id';

        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = $this->fb_base_url . 'me/adaccounts?fields=' . $fields;

        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $user_access_token,
        ])->get($url);

        $response = $http->json();
        LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $http->status(), SocialConfigController::class, 'getadsAccountManager');

        return $response['data'];
    }

    /**
     * Method to generate the Facebook access token and get
     * the basic profile details about the account.
     *
     * @return RedirectResponse
     */
    public function getfbTokenBack(Request $request)
    {
        $code = $request['code'];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $accessTokenUrl = $this->fb_base_url .
            'oauth/access_token?client_id=' .
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

        $meUrl = $this->fb_base_url . 'me/?access_token=' . $response['access_token'];
        $meHttp = Http::get($meUrl);
        $meResponse = $meHttp->json();

        LogRequest::log($startTime, $meUrl, 'GET', json_encode([]), $meResponse, $meHttp->status(), SocialConfigController::class, 'getfbTokenBack');

        SocialConfig::create([
            'account_id' => $meResponse['id'],
            'name' => $meResponse['name'],
            'token' => $response['access_token'],
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
            $url = sprintf($this->fb_base_url . $request->page_id . '?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->page_token);
            $http = Http::get($url);
            $response = $http->json();
            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $http->status(), SocialConfigController::class, 'store');
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
        $config = SocialConfig::findorfail($request->id);

        $pageId = $request->page_id;
        $data = $request->validated();

        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        if (isset($request->adsmanager)) {
            $data['ads_manager'] = $request->adsmanager;
        }
        if ($request->platform == 'instagram') {
            $url = sprintf($this->fb_base_url . $request->page_id . '?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->token);
            $http = Http::get($url);
            $response = $http->json();
            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $http->status(), SocialConfigController::class, 'edit');
            if ($id = $response['instagram_business_account']['id']) {
                $data['account_id'] = $id;
            } else {
                return redirect()->back()->withError('Page Linked Account ID not found.');
            }
        } else {
            $data['account_id'] = $pageId;
        }
        $data['page_language'] = $request->page_language;

        $config->update($data);
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

        return Response::jsonResponse(message: 'Config Deleted');
    }
}
