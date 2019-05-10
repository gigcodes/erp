<?php

namespace App\Services\Instagram;


use Carbon\Carbon;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

class Hashtags {

    private $instagram;
    private $token;

    public function login() {
        $instagram = new Instagram();
        $instagram->login('sololuxury.official', 'Insta123!');
        $this->token = Signatures::generateUUID();
        $this->instagram = $instagram;
    }

    public function getMediaCount($hashtag) {
        return $this->instagram->hashtag->getInfo($hashtag)->asArray()['media_count'];
    }

    public function getFeed($hashtag, $maxId = '')
    {
        $media = $this->instagram->hashtag->getFeed($hashtag, $this->token, $maxId);
        $medias = $media->asArray();
        $maxId = $medias['next_max_id'] ?? 'END';
        $medias = $medias['items'];
        $ranked_medias = $media->asArray()['ranked_items'] ?? [];

        $medias = array_merge($ranked_medias, $medias);
        $filteredMedia = [];

        foreach ($medias as $item) {
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

            if ($this->pointInPolygon($x, $y)) {
                $filteredMedia[] = [
                    'username' => $item['user']['username'],
                    'media_id' => $item['id'],
                    'code' => $item['code'],
                    'caption' => $item['caption']['text'],
                    'like_count' => $item['like_count'],
                    'comment_count' => $item['comment_count'] ?? '0',
                    'media_type' => $item['media_type'],
                    'media' => $media,
                    'comments' => $comments,
                    'location' => $item['location'],
                    'created_at' => Carbon::createFromTimestamp($item['taken_at'])->diffForHumans(),
                ];
            }
        }

        return [$filteredMedia, $maxId];
    }

    public function getRelatedHashtags($hashtag)
    {
        return $this->instagram->hashtag->getRelated($hashtag)->asArray();
    }

    function pointInPolygon($x,$y) {
        $polySides = 18;
        $polyX[] = '7.687694'; $polyY[] = '77.379244';
        $polyX[] = '9.859114';  $polyY[] = '80.279635';
        $polyX[] = '15.261506';  $polyY[] = '80.543307';
        $polyX[] = '19.371891';  $polyY[] = '85.640963';
        $polyX[] = '20.198903';  $polyY[] = '87.047213';
        $polyX[] = '21.185539';  $polyY[] = '87.222994';
        $polyX[] = '21.512977';  $polyY[] = '88.980807';
        $polyX[] = '24.585693';  $polyY[] = '87.926119';
        $polyX[] = '25.302914';  $polyY[] = '88.453463';
        $polyX[] = '26.173779';  $polyY[] = '87.926119';
        $polyX[] = '28.515619';  $polyY[] = '80.015963';
        $polyX[] = '30.130449';  $polyY[] = '80.8157';
        $polyX[] = '32.235619';  $polyY[] = '78.354763';
        $polyX[] = '32.680581';  $polyY[] = '76.245388';
        $polyX[] = '30.43404';  $polyY[] = '74.663357';
        $polyX[] = '27.200461';  $polyY[] = '71.147732';
        $polyX[] = '27.200461';  $polyY[] = '69.214138';
        $polyX[] = '23.548428';  $polyY[] = '68.686794';
        $j = $polySides-1 ;
        $oddNodes = 0;
        for ($i=0; $i<$polySides; $i++) {
            if (($polyY[$i]<$y && $polyY[$j]>=$y) ||  ($polyY[$j]<$y && $polyY[$i]>=$y)) {
                if ($polyX[$i]+($y-$polyY[$i])/($polyY[$j]-$polyY[$i])*($polyX[$j]-$polyX[$i])<$x) {
                    $oddNodes=!$oddNodes;
                }
            }
            $j=$i;
        }

        return $oddNodes;
    }
}
