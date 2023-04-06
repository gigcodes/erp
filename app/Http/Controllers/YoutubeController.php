<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use Carbon\Carbon;
use Google\Auth\OAuth2;
use Illuminate\Http\Request;
use App\Models\YoutubeChannel;
use Google\Auth\CredentialsLoader;
use App\Models\StoreWebsiteYoutube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchYoutubeChannelData;
use Laravel\Socialite\Facades\Socialite;

class YoutubeController extends Controller
{

    /*
    * used to get google refresh token for ads
    */
    public function refreshToken(Request $request)
    {
        $google_redirect_url = route('youtubeaccount.get-refresh-token');

        $PRODUCTS = [
            ['YouTube API', config('youtube.YOUTUBE_API_SCOPE')],
        ];

        $client_id = $request->client_id;
        $client_secret = $request->client_secret;
        Session::put('client_id', $client_id);
        Session::put('client_secret', $client_secret);
        Session::save();

        $api = intval(0);

        $scopes = ['Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl', 'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit'];


        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('youtube.GOOGLE_ADS_AUTHORIZATION_URI'),
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
        //header('Location: '.$oauth2->buildFullAuthorizationUri());
    }

    public function createChanel(Request $request)
    {

        //create account
        $this->validate($request, [
            'store_websites' => 'required',
            // 'config_file_path' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
        ]);

        try {

            $input = $request->all();
            $createChannel = YoutubeChannel::create($input);
            FetchYoutubeChannelData::dispatch($createChannel);

            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Youtube Channel added successfully');
        } catch (Exception $e) {
            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }

    /*
    * Refresh token Redirect API
    */
    public function getRefreshToken(Request $request)
    {
        $google_redirect_url = route('youtubeaccount.get-refresh-token');
        $api = intval(0);
        // $PRODUCTS = [
        //     ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
        //     ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
        //     ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE').' '
        //         .config('google.GOOGLE_ADS_MANAGER_API_SCOPE'), ],
        // ];
        $PRODUCTS = [
            ['YouTube API', config('youtube.YOUTUBE_API_SCOPE')],
        ];

        $scopes = ['Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl', 'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit'];

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

            return view('youtube.chanel.view_token', ['refresh_token' => $authToken['refresh_token'], 'access_token' => $authToken['access_token']]);
        } else {
            return redirect('/youtube/add-chanel')->with('message', 'Unable to Get Tokens ');
        }
    }

    public function youtubeRedirect(Request $request)
    {
        return Socialite::driver('youtube')->with(['state' => $request->id, 'access_type' => 'offline', 'prompt' => 'consent select_account', 'scope' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit', 'scope' => 'https://www.googleapis.com/auth/youtube.force-ssl'])->redirect();
    }

    public function updateYoutubeAccessToken($websiteId)
    {
        try {
            $websiteData =  StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();

            $params = [
                'refresh_token' => $websiteData->refresh_token,
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com'
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $websiteData->access_token = $response['access_token'];
            $expireIn = !empty($response['expires_in']) ? $response['expires_in'] : null;
            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }
            $websiteData->token_expire_time = $expireIn;
            $websiteData->save();
        } catch (\Exception $e) {
            Log::info(__('failedToUpdateUserAccessToken', [$websiteData]));
            Log::info($e->getMessage());
        }
    }

    public function creteChanel(Request $request)
    {
        // Create Chanel Means Get Chanel Data  using refresh Token.
        $query = YoutubeChannel::query();
        if ($request->website) {
            $query = $query->where('store_websites', $request->website);
        }

        // Account name meand Channel name

        if ($request->accountname) {
            $query = $query->where('chanel_name', 'LIKE', '%' . $request->accountname . '%');
        }

        $googleadsaccount = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('youtube.chanel.filter-channel', compact('googleadsaccount'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $googleadsaccount->render(),
                'count' => $googleadsaccount->total(),
            ], 200);
        }

        $store_website = \App\StoreWebsite::all();
        $totalentries = $googleadsaccount->count();



        return view('youtube.chanel.chanel-create', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries, 'store_website' => $store_website]);
    }

    // public function regenerateToken($websiteId)
    // {
    //     $websiteData =  StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();
    //     $tokenExpireTime = strtotime(Carbon::parse($websiteData->token_expire_time));
    //     $currentTime = strtotime(Carbon::now());
    //     if (($tokenExpireTime - $currentTime) <= 0) {
    //         $this->updateYoutubeAccessToken($websiteId);
    //     }

    // }
    //$auth->auth()->regenerateToken($campaignuserMeta);

    public function GetChanelData()
    {
        $user  = Socialite::driver('youtube')->stateless()->user();
        $websiteId = request()->input('state');
        $socialsObj = StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();

        if (empty($socialsObj)) {
            $expireIn = !empty($user->accessTokenResponseBody['expires_in']) ? $user->accessTokenResponseBody['expires_in'] : null;
            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }

            $data = [
                'access_token' => !empty($user->accessTokenResponseBody['access_token']) ? $user->accessTokenResponseBody['access_token'] : null,
                'refresh_token' => !empty($user->accessTokenResponseBody['refresh_token']) ? $user->accessTokenResponseBody['refresh_token'] : null,
                'store_website_id' => !empty(request()->input('state')) ? request()->input('state') : null,
                'token_expire_time' => $expireIn

            ];
            StoreWebsiteYoutube::create($data);
        }
        $this->regenerateToken($websiteId);


        if (!empty($user)) {
            return redirect()->route('chanelList', ['website_id' => $websiteId]);
        }

        abort(404);
    }

    public function VideoListByChanelId(Request $request)
    {

        $websiteId = !empty($request->route('websiteId')) ? $request->route('websiteId') : null;
        $chanelId = !empty($request->route('chanelId')) ? $request->route('chanelId') : null;
        if (empty($websiteId) || empty($chanelId)) {
            abort(404);
        }
        $accessToken = $this->getAccessToken($websiteId);
        $this->regenerateToken($websiteId);

        $videoIds = $this->getVideoIds($accessToken, $chanelId);

        $videoData = $this->getVideo($accessToken, $videoIds);
        return view('youtube.chanel.video.video-list', compact('videoData', 'websiteId'));
    }

    public function CommentByVideoId(Request $request)
    {

        $websiteId = !empty($request->route('websiteId')) ? $request->route('websiteId') : null;
        $videoId = !empty($request->route('videoId')) ? $request->route('videoId') : null;
        $accessToken = $this->getAccessToken($websiteId);
        $this->regenerateToken($websiteId);

        $commentListObjs = Http::withToken($accessToken)
            ->get('https://youtube.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=5&mine=true&videoId=' . $videoId)
            ->json();


        $comments = [];
        if ($commentListObjs['kind'] == 'youtube#commentThreadListResponse') {
            $videoLists = $commentListObjs['items'];


            foreach ($videoLists as $video) {
                if ($video['kind'] == 'youtube#commentThread') {
                    array_push($comments, $video['snippet']['topLevelComment']['snippet']);
                }
            }
        }
        return view('youtube.chanel.comment.comment-list', compact('comments', 'websiteId'));
    }

    public function getVideo($accessToken, $videoId)
    {
        $videosArr = [];

        $videos = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&maxResults=5&id=' . implode(',', $videoId))
            ->json();

        if ($videos['kind'] == 'youtube#videoListResponse') {
            $videoLists = $videos['items'];
            foreach ($videoLists as $video) {
                if (!empty($video)) {
                    $videoObj = [];
                    if ($video['kind'] == 'youtube#video') {
                        $videoObj['media_id'] = $video['id'];
                        $videoObj['provider'] = 'youtube';
                        $interval = new \DateInterval($video['contentDetails']['duration']);
                        $videoObj['duration'] = $interval->h * 3600 + $interval->i * 60 + $interval->s; //Store in seconds
                        $videoObj['type'] = 'video';
                        $videoObj['title'] = $video['snippet']['title'];
                        $videoObj['link'] = 'https://www.youtube.com/embed/' . $video['id'];
                        $videoObj['like_count'] = $video['statistics']['likeCount'];
                        $videoObj['view_count'] = $video['statistics']['viewCount'];
                        $videoObj['title'] = $video['snippet']['title'];
                        $videoObj['create_time'] = date('Y-m-d H:i:s', strtotime($video['snippet']['publishedAt']));
                        $videoObj['created_at'] = now();
                    }
                    array_push($videosArr, $videoObj);
                }
            }
        }

        return $videosArr;
    }

    public function getVideoIds($accessToken, $chanelId)
    {
        $videoId = [];

        $videoListObjs = Http::withToken($accessToken)
            ->get('https://youtube.googleapis.com/youtube/v3/search?part=snippet&maxResults=5&channelId=' . $chanelId)
            ->json();

        if ($videoListObjs['kind'] == 'youtube#searchListResponse') {
            $videoLists = $videoListObjs['items'];
            foreach ($videoLists as $video) {
                if ($video['id']['kind'] == 'youtube#video') {
                    array_push($videoId, $video['id']['videoId']);
                }
            }
        }

        return $videoId;
    }

    public function getAccessToken($websiteId)
    {
        return StoreWebsiteYoutube::where('store_website_id', $websiteId)->pluck('access_token')->first();
    }

    // public function chanelList(Request $request)
    // {

    //     $accessToken = $this->getAccessToken($request->website_id);
    //     $this->regenerateToken($request->website_id);

    //     $youtubeChannels = Http::withToken($accessToken)
    //         ->get('https://www.googleapis.com/youtube/v3/channels?part=id,topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
    //         ->json();


    //     $chanelsList = [];
    //     if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {

    //         if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {
    //             foreach ($youtubeChannels['items'] as $youtubeChannel) {
    //                 $chanelsList = $youtubeChannel;
    //             }
    //             $websiteId = $request->website_id;
    //             return view('youtube.chanel.chanel-list', compact('chanelsList', 'websiteId'));
    //         }
    //     }
    // }

    public function getChannelList($providerToken)
    {
        return  Http::withToken($providerToken)
            ->get('https://www.googleapis.com/youtube/v3/channels?part=topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
            ->json();
    }

    public function editChannel($id)
    {
        $store_website = \App\StoreWebsite::all();
        $googleAdsAc = YoutubeChannel::findOrFail($id);
        return $googleAdsAc;
    }

    public function updateChannel(Request $request)
    {

        $account_id = $request->account_id;
        //update account
        //create account
        $this->validate($request, [
            'store_websites' => 'required',
            // 'config_file_path' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
            'chanel_name' => 'required',
        ]);

        try {
            $input = $request->all();

            $googleadsAcQuery = new YoutubeChannel();
            $googleadsAc = $googleadsAcQuery->find($account_id);
            $googleadsAc->fill($input);
            $googleadsAc->save();

            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Channel updated successfully');
        } catch (Exception $e) {
            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }
}
