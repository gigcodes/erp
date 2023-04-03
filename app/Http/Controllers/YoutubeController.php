<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StoreWebsiteYoutube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class YoutubeController extends Controller
{
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
            if(!empty($expireIn)){
                $currentTime = strtotime(Carbon::now());
                $expireIn= Carbon::createFromTimestamp(($currentTime + $expireIn));       
            }
            $websiteData->token_expire_time = $expireIn;
            $websiteData->save();
        } catch (\Exception $e) {
            Log::info(__('failedToUpdateUserAccessToken', [$websiteData]));
            Log::info($e->getMessage());
        }
    }

    public function regenerateToken($websiteId)
    {
        $websiteData =  StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();
        $tokenExpireTime = strtotime(Carbon::parse($websiteData->token_expire_time));
        $currentTime = strtotime(Carbon::now());

        if (($tokenExpireTime - $currentTime) <= 0) {
            $this->updateYoutubeAccessToken($websiteId);
        }

    }
    //$auth->auth()->regenerateToken($campaignuserMeta);

    public function GetChanelData()
    {
        $user  = Socialite::driver('youtube')->stateless()->user();
        $websiteId = request()->input('state');
        $socialsObj = StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();
       
        if (empty($socialsObj)) {
            $expireIn = !empty($user->accessTokenResponseBody['expires_in']) ? $user->accessTokenResponseBody['expires_in'] : null;
            if(!empty($expireIn)){
                $currentTime = strtotime(Carbon::now());
                $expireIn= Carbon::createFromTimestamp(($currentTime + $expireIn));       
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

    public function chanelList(Request $request)
    {

        $accessToken = $this->getAccessToken($request->website_id);
        $this->regenerateToken($request->website_id);

        $youtubeChannels = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/channels?part=id,topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
            ->json();

        $chanelsList = [];
        if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {

            if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {
                foreach ($youtubeChannels['items'] as $youtubeChannel) {
                    $chanelsList = $youtubeChannel;
                }
                $websiteId = $request->website_id;
                return view('youtube.chanel.chanel-list', compact('chanelsList', 'websiteId'));
            }
        }
    }

    public function getChannelList($providerToken)
    {
        return  Http::withToken($providerToken)
            ->get('https://www.googleapis.com/youtube/v3/channels?part=topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
            ->json();
    }
}
