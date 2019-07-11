<?php

namespace App\Services\Instagram;


use App\HashTag;
use App\InstagramUsersList;
use App\TargetLocation;
use Carbon\Carbon;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

class Hashtags {

    /**
     * Instagram $instagram
     */
    public $instagram;
    private $token;

    public function login() {
        $instagram = new Instagram();
//        $instagram->login('rishabh_aryal', 'R1shabh@12345');
        $instagram->login('sololuxury.official', "NcG}4u'z;Fm7");
        $this->token = Signatures::generateUUID();
        $this->instagram = $instagram;
    }

    public function getMediaCount($hashtag) {
        return $this->instagram->hashtag->getInfo($hashtag)->asArray()['media_count'];
    }

    public function getFeed($hashtag, $maxId = '', $country = null, $keywords = null)
    {
        $media = $this->instagram->hashtag->getFeed($hashtag, $this->token, $maxId);
        $medias = $media->asArray();
        $maxId = $medias['next_max_id'] ?? 'END';
        $medias = $medias['items'];
        $ranked_medias = $media->asArray()['ranked_items'] ?? [];

        $medias = array_merge($ranked_medias, $medias);


        $filteredMedia = [];

        foreach ($medias as $item) {

            $cap = $item['caption']['text'];
            $show = true;
            if ($keywords) {
                $show = false;
                foreach ($keywords as $keyword) {
                    if (stripos(strtoupper($cap), strtoupper($keyword)) !== false) {
                        $show = true;
                        continue;
                    }
                }
            }

            if (!$show) {
                continue;
            }

            preg_match_all("/(#\w+)/", $cap, $matches);

            $matches = $matches[0];
            foreach ($matches as $match) {
                $match = str_replace('#', '', $match);
                $ht = HashTag::where('hashtag', $match)->first();

                if (!$ht) {
                    $ht = new HashTag();
                    $ht->hashtag = $match;
                    $ht->rating = 5;
                    $ht->save();
                }
            }


            if ($item['media_type'] === 1) {
                $media = $item['image_versions2']['candidates'][1]['url'];
            } else if ($item['media_type'] === 2) {
                $media = $item['video_versions'][0]['url'];
            } else if ($item['media_type'] === 8) {
                $crousal = $item['carousel_media'];
                $media = [];
                foreach ($crousal as $cro) {
                    if ($cro['media_type'] === 1) {
                        $media[] = [
                            'media_type' => 1,
                            'url' => $cro['image_versions2']['candidates'][0]['url']
                        ];
                    } else if ($cro['media_type'] === 2) {
                        $media[] = [
                            'media_type' => 2,
                            'url' => $cro['video_versions'][0]['url']
                        ];
                    }
                }
            }


            $comments = [];

            if (isset($item['comment_count']) && $item['comment_count']) {
                $comments = $item['preview_comments'];
            }

            $x =  $item['location']['lat'] ?? 0;
            $y = $item['location']['lng'] ?? 0;

            $point = new Location();

            $target = TargetLocation::where('region', $country)->first();
            if (!$target) {
                $filteredMedia[$item['taken_at']] = [
                    'hashtag' => $hashtag,
                    'username' => $item['user']['username'],
                    'user_id' => $item['user']['pk'],
                    'media_id' => $item['id'],
                    'code' => $item['code'],
                    'caption' => $item['caption']['text'],
                    'like_count' => $item['like_count'],
                    'comment_count' => $item['comment_count'] ?? '0',
                    'media_type' => $item['media_type'],
                    'media' => $media,
                    'comments' => $comments,
                    'location' => $item['location'] ?? '',
                    'ts' => $item['taken_at'] ?? 0,
                    'created_at' => Carbon::createFromTimestamp($item['taken_at'])->diffForHumans(),
                    'posted_at' => Carbon::createFromTimestamp($item['taken_at'])->toDateTimeString(),
                ];
            } else {
                $location = $point->pointInParticularLocation($x, $y, $target);
                if ($location[0]) {

                    $l = InstagramUsersList::where('user_id', $item['user']['pk'])->first();

                    if (!$l) {
                        $l = new InstagramUsersList();
                    }

                    $l->username = $item['user']['username'];
                    $l->user_id = $item['user']['pk'];
                    $l->image_url = $item['user']['profile_pic_url'];
                    $l->bio = $item['user']['biography'] ?? 'N/A';
                    $l->rating = 0;
                    $l->location_id = $location[1]->id ?? 1;
                    $l->because_of = "Hashtags: $hashtag";
                    $l->save();

                    $filteredMedia[$item['taken_at']] = [
                        'hashtag' => $hashtag,
                        'username' => $item['user']['username'],
                        'user_id' => $item['user']['pk'],
                        'media_id' => $item['id'],
                        'code' => $item['code'],
                        'caption' => $item['caption']['text'],
                        'like_count' => $item['like_count'],
                        'comment_count' => $item['comment_count'] ?? '0',
                        'media_type' => $item['media_type'],
                        'media' => $media,
                        'comments' => $comments,
                        'location' => $item['location'] ?? '',
                        'ts' => $item['taken_at'] ?? 0,
                        'created_at' => Carbon::createFromTimestamp($item['taken_at'])->diffForHumans(),
                        'posted_at' => Carbon::createFromTimestamp($item['taken_at'])->toDateTimeString(),
                    ];
                }
            }
        }

        return [$filteredMedia, $maxId];
    }

    public function getRelatedHashtags($hashtag)
    {
        return $this->instagram->hashtag->getRelated($hashtag)->asArray();
    }
}
