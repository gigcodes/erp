<?php

namespace App\Library\Youtube;

use Carbon\Carbon;
use App\Models\YoutubeVideo;
use App\Models\YoutubeChannel;
use App\Models\YoutubeComment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Helper
{

    public static function getAccessTokenFromRefreshToken($refreshToken, $id)
    {
        try {

            $params = [
                'refresh_token' => $refreshToken,
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com'
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $expireIn = !empty($response['expires_in']) ? $response['expires_in'] : null;

            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
                YoutubeChannel::where('id', $id)->update(['token_expire_time' => $expireIn]);
            }

            return $response['access_token'];

            // $expireIn = !empty($response['expires_in']) ? $response['expires_in'] : null;
            // if(!empty($expireIn)){
            //     $currentTime = strtotime(Carbon::now());
            //     $expireIn= Carbon::createFromTimestamp(($currentTime + $expireIn));       
            // }
            // $websiteData->token_expire_time = $expireIn;
            // $websiteData->save();
        } catch (\Exception $e) {
          
            Log::info(__('failedGetAccessToken', ['something went wrong']));
            Log::info($e->getMessage());
        }
    }

    public static function getChanelData($accessToken, $youtubeChanelTableId)
    {
        self::regenerateToken($youtubeChanelTableId);

        $youtubeChannels = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/channels?part=id,topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
            ->json();


        $chanelsList = [];
        if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {

            if (!empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {
                foreach ($youtubeChannels['items'] as $youtubeChannel) {
                    $chanelsList = $youtubeChannel;
                }
            }
        }
        return $chanelsList;
    }


    public static function regenerateToken($id)
    {
        $youtubeChannelData =  YoutubeChannel::where('id', $id)->first();
        $tokenExpireTime = strtotime(Carbon::parse($youtubeChannelData->token_expire_time));
        $currentTime = strtotime(Carbon::now());
        if (($tokenExpireTime - $currentTime) <= 0) {
            self::updateYoutubeAccessToken($id);
        }
    }

    public static function updateYoutubeAccessToken($id)
    {
        try {
            $youtubeChannel =  YoutubeChannel::where('id', $id)->first();

            $params = [
                'refresh_token' => $youtubeChannel->refresh_token,
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com'
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $youtubeChannel->access_token = $response['access_token'];
            $expireIn = !empty($response['expires_in']) ? $response['expires_in'] : null;
            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }
            $youtubeChannel->token_expire_time = $expireIn;
            $youtubeChannel->save();
        } catch (\Exception $e) {
            Log::info(__('failedToUpdateUserAccessToken', [$youtubeChannel]));
            Log::info($e->getMessage());
        }
    }


    public static function VideoListByChanelId($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);

        $videoIds = self::getVideoIds($youtubeChanelTableId, $accessToken, $chanelId);

        $videoData = self::getVideo($youtubeChanelTableId, $accessToken, $videoIds);

        return $videoData;
    }

    public static function getVideoAndInsertDB($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);
        $videoData = self::VideoListByChanelId($youtubeChanelTableId, $accessToken, $chanelId);
        $videoIds = [];
        if (!empty($videoData)) {
            foreach ($videoData as $value) {
                $videoAdd = [];
                $videoAdd['youtube_channel_id'] = $youtubeChanelTableId;
                $videoAdd['media_id'] = $value['media_id'];
                $videoAdd['title'] = $value['title'];
                $videoAdd['link'] = $value['link'];
                $videoAdd['like_count'] = $value['like_count'];
                $videoAdd['view_count'] = $value['view_count'];
                $videoAdd['create_time'] = $value['create_time'];
                $videoAdd['description'] = $value['description'];
                $videoAdd['dislike_count'] = $value['dislike_count'];
                $videoAdd['comment_count'] = $value['comment_count'];
                $videoAdd['channel_id'] = $value['channel_id'];
                $youtubeVideoData = YoutubeVideo::create($videoAdd);

                self::getCommentAndInsertInDB($youtubeVideoData->id, $youtubeChanelTableId, $value['media_id'], $accessToken);
                array_push($videoIds, $videoAdd['media_id']);
            }
        }

        return $videoIds;
    }

    public static function getCommentAndInsertInDB($youtubeVideoTableId, $youtubeChanelTableId, $videoId, $accessToken)
    {
        self::regenerateToken($youtubeChanelTableId);

        $commentListObjs = Http::withToken($accessToken)
            ->get('https://youtube.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&mine=true&videoId=' . $videoId)
            ->json();

        $comments = [];
        if ($commentListObjs['kind'] == 'youtube#commentThreadListResponse') {
            $videoLists = $commentListObjs['items'];
            foreach ($videoLists as $video) {
                if ($video['kind'] == 'youtube#commentThread') {

                    $commentInsertData['youtube_video_id'] = $youtubeVideoTableId;
                    $commentInsertData['title'] = !empty($video['snippet']['topLevelComment']['snippet']['textOriginal']) ? $video['snippet']['topLevelComment']['snippet']['textOriginal'] : '';
                    $commentInsertData['like_count'] = !empty($video['snippet']['topLevelComment']['snippet']['likeCount']) ? $video['snippet']['topLevelComment']['snippet']['likeCount'] : '';
                    $commentInsertData['create_time'] = !empty($video['snippet']['topLevelComment']['snippet']['publishedAt']) ? $video['snippet']['topLevelComment']['snippet']['publishedAt'] : '';
                    $commentInsertData['video_id'] = !empty($video['snippet']['topLevelComment']['snippet']['videoId']) ? $video['snippet']['topLevelComment']['snippet']['videoId'] : '';
                    $commentInsertData['comment_id'] = !empty($video['id']) ? $video['id'] : '';
                    YoutubeComment::create($commentInsertData);
                }
            }
        }
    }

    public static function getVideo($youtubeChanelTableId, $accessToken, $videoId)
    {
        self::regenerateToken($youtubeChanelTableId);
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
                        // $interval = new \DateInterval($video['contentDetails']['duration']);
                        // $videoObj['duration'] = $interval->h * 3600 + $interval->i * 60 + $interval->s; //Store in seconds
                        // $videoObj['type'] = 'video';
                        $videoObj['title'] = $video['snippet']['title'];
                        $videoObj['link'] = 'https://www.youtube.com/embed/' . $video['id'];
                        $videoObj['like_count'] = $video['statistics']['likeCount'];
                        $videoObj['view_count'] = $video['statistics']['viewCount'];
                        $videoObj['dislike_count'] = $video['statistics']['dislikeCount'];
                        $videoObj['comment_count'] = $video['statistics']['commentCount'];
                        $videoObj['title'] = $video['snippet']['title'];
                        $videoObj['description'] = $video['snippet']['description'];
                        $videoObj['create_time'] = date('Y-m-d H:i:s', strtotime($video['snippet']['publishedAt']));
                        $videoObj['created_at'] = now();
                        $videoObj['channel_id'] = $video['snippet']['channelId'];
                    }
                    array_push($videosArr, $videoObj);
                }
            }
        }

        return $videosArr;
    }

    public static function getVideoIds($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);
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
}
