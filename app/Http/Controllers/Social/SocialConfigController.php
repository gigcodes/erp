<?php

namespace App\Http\Controllers\Social;

use App\Setting;
use App\Language;
use App\StoreWebsite;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SocialAdAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
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
            'websites' => StoreWebsite::select('id', 'title')->get(),
            'user_names' => SocialConfig::select('email')->distinct()->get(),
            'platforms' => SocialConfig::select('platform')->distinct()->get(),
            'ad_accounts' => SocialAdAccount::where('status', 1)->get()->toArray(),
            'languages' => Language::get(),
            'selected_website' => $request->store_website_id,
            'selected_user_name' => $request->user_name,
            'selected_platform' => $request->platform,
        ];
    }

    public function adStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'page_token' => 'required',
            'store_website_id' => 'required',
            'ad_account_id' => 'required',
            'status' => 'required',
        ]);

        SocialAdAccount::create($request->all(['name', 'store_website_id', 'ad_account_id', 'page_token', 'status']));

        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    public function getNeverExpiringToken(array $data): string|bool
    {
        $url = $this->fb_base_url . 'oauth/access_token?grant_type=fb_exchange_token&client_id=' . config('facebook.config.app_id')
            . '&client_secret=' . config('facebook.config.app_secret') . '&fb_exchange_token=' . $data['page_token'];
        $http = Http::get($url);
        $response = $http->json();

        if ($data['platform'] == 'instagram') {
            return $response['access_token'];
        }

        if (isset($response['error'])) {
            return false;
        }
        $long_lived_token = $response['access_token'];
        $permanent_token_url = $this->fb_base_url . $data['page_id'] . '?fields=access_token&access_token=' . $long_lived_token;
        $httpPT = Http::get($permanent_token_url);
        $ptResponse = $httpPT->json();
        if (! isset($ptResponse['access_token'])) {
            return false;
        }

        return $ptResponse['access_token'];
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
        $neverExpiringToken = $this->getNeverExpiringToken($data);
        if (! $neverExpiringToken) {
            return redirect()->back()->withError('Unable to refactor the token. Kindly validate it');
        }
        $data['account_id'] = $pageId;
        $data['page_token'] = $neverExpiringToken;

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

        $neverExpiringToken = $this->getNeverExpiringToken($data);
        if (! $neverExpiringToken) {
            return redirect()->back()->withError('Unable to refactor the token. Kindly validate it');
        }

        if (isset($request->adsmanager)) {
            $data['ads_manager'] = $request->adsmanager;
        }
        $data['account_id'] = $pageId;
        $data['page_language'] = $request->page_language;
        $data['page_token'] = $neverExpiringToken;

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
