<?php

namespace App\Http\Controllers;

use App\Account;
use GuzzleHttp\Client;
use InstagramAPI\Instagram;
use Illuminate\Http\Request;
use Wa72\HtmlPageDom\HtmlPageCrawler;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class AccountController extends Controller
{
    public function show($id) {
        $account = Account::findOrFail($id);
        $accounts = Account::where('platform', 'instagram')->get();
        return view('reviews.show', compact('account', 'accounts'));
    }

    public function sendMessage($id, Request $request, Instagram $instagram) {

        $this->validate($request, [
            'username' => 'required',
            'message' => 'required'
        ]);

        $account = Account::findOrFail($id);
        $last_name = $account->last_name;
        $password = $account->password;

        $apiUrl = "https://www.instagram.com/$request->username";
        $guzzle = new Client();
        $data = $guzzle->get($apiUrl);
        $content = $data->getBody()->getContents();

        $c = new HtmlPageCrawler($content);
        $firstScript = $c->filter('body script')->getInnerHtml();

        $firstScript = str_replace('window._sharedData = ', '', $firstScript);

        $firstScript = substr($firstScript, 0, strlen($firstScript)-1);

        $firstScript = json_decode($firstScript, true);

        if (!isset($firstScript['entry_data']['ProfilePage'][0]['graphql']['user']['id'])) {
            return response()->json([
                'status' => 'User Not Found!'
            ]);
        }

        $id = $firstScript['entry_data']['ProfilePage'][0]['graphql']['user']['id'];

        $instagram->login($last_name, $password);
        $instagram->direct->sendText(['users' => [$id]], $request->get('message'));
        return response()->json([
            'status' => 'Message Sent successfully!'
        ]);

    }
}
