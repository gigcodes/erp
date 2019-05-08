<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class InstagramProfileController extends Controller
{
    public function index() {
        return view('instagram.profile.list');
    }

    public function show($id, Request $request) {
            $customers = Customer::where('instahandler', '!=', '')->where('rating', '>', 5)->orderBy('rating', 'DESC')->paginate(4);

            $customers = $customers->toArray();

            $customerProfiles = $customers['data'];

            $self = $this;

            $instagramProfiles = array_map(function($customer) use ($self) {
                return $self->getInstagramUserData($customer);
            }, $customerProfiles);

            return response()->json([$instagramProfiles, $customers]);
    }

    private function getInstagramUserData($customer) {
        $instagram = new Instagram();
        $instagram->login(env('IG_USERNAME', 'sololuxury.official'), env('IG_PASSWORD', 'Insta123!'));
        try {
            $profileData = $instagram->people->getInfoByName($customer['instahandler'])->asArray();
        } catch (\Exception $exception) {
            $profileData = [];
        }

        if (!isset($profileData['user'])) {
            return [];
        }

        $profileData = $profileData['user'];

        return [
            'id' => $profileData['pk'],
            'name' => $profileData['full_name'],
            'username' => $profileData['username'],
            'followers' => $profileData['follower_count'],
            'following' => $profileData['following_count'],
            'media' => $profileData['media_count'],
            'profile_pic_url' => $profileData['profile_pic_url'],
            'is_verified' => $profileData['is_verified'],
            'bio' => $profileData['biography'],
            'customer' => $customer
        ];

    }

    public function getFollowers($id) {
        $instagram = new Instagram();
        $instagram->login(env('IG_USERNAME', 'sololuxury.official'), env('IG_PASSWORD', 'Insta123!'));
        $rankToken = Signatures::generateUUID();
        $followers = $instagram->people->getFollowers($id, $rankToken);

        dd($followers);

    }
}
