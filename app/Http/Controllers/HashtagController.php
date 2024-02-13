<?php

namespace App\Http\Controllers;

use App\Account;
use App\Setting;
use App\Customer;
use App\LogRequest;
use App\Mailinglist;
use App\ScrapInfluencer;
use App\InfluencerKeyword;
use App\InfluencersHistory;
use App\MailinglistTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HashtagController extends Controller
{
    private $maxId;

    public $platformsId;

    public function __construct(Request $request)
    {
        $this->platformsId = 1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * Show all the hashtags we have saved
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * Create a new hashtag entry
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * Show hashtag
     */
    public function edit($hashtag, Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * @SWG\Get(
     *   path="/hashtags",
     *   tags={"Hashtags"},
     *   summary="Get hashtags",
     *   operationId="get-hashtags",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function rumCommand(Request $request)
    {
        $id = $request->id;
        $account = $request->account;

        try {
            $hashTag = \App\HashTag::find($id);
            if ($hashTag) {
                $hashTag->instagram_account_id = $account;
                $hashTag->save();
            }

            $cmd = 'ps waux | grep competitors:process-local-users\ ' . $id;
            $export = shell_exec($cmd);
            $export = trim($export);
            //getting username
            $cmd = 'echo $USER';
            $username = shell_exec($cmd);
            $username = trim($username);

            $re = '/' . $username . '(\s*)(\d*)/m';

            preg_match_all($re, $export, $matches, PREG_SET_ORDER, 0);

            if (count($matches) == 0 || count($matches) == 1 || count($matches) == 2) {
                $cmd = 'php ' . base_path() . '/artisan competitors:process-local-users ' . $id . ' &';
                $export = shell_exec($cmd);

                return ['success' => true, 'message' => 'Process Started Running'];
            } elseif (count($matches) == 3 || count($matches) == 4) {
                return ['success' => true, 'message' => 'Process Is Already Running'];
            }

            return ['error' => true, 'message' => 'Something went wrong'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Something went wrong'];
        }
    }

    public function killCommand(Request $request)
    {
        try {
            $id = $request->id;
            $cmd = 'ps waux | grep competitors:process-local-users\ ' . $id;
            $export = shell_exec($cmd);
            $export = trim($export);
            //getting username
            $cmd = 'echo $USER';
            $username = shell_exec($cmd);
            $username = trim($username);

            $re = '/' . $username . '(\s*)(\d*)/m';

            preg_match_all($re, $export, $matches, PREG_SET_ORDER, 0);

            if (count($matches) == 0 || count($matches) == 1 || count($matches) == 2) {
                return ['success' => true, 'message' => 'Process Is Not Running'];
            } elseif (count($matches) == 3 || count($matches) == 4) {
                foreach ($matches as $match) {
                    if (isset($match[2])) {
                        $cmd = 'kill -9 ' . $match[2];
                        $export = shell_exec($cmd);
                    }
                }
            }

            return ['success' => true, 'message' => 'Process Killed successfuly'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Something went wrong'];
        }
    }

    public function checkStatusCommand(Request $request)
    {
        try {
            $id = $request->id;
            $cmd = 'ps waux | grep competitors:process-local-users\ ' . $id;
            $export = shell_exec($cmd);
            $export = trim($export);
            //getting username
            $cmd = 'echo $USER';
            $username = shell_exec($cmd);
            $username = trim($username);

            $re = '/' . $username . '(\s*)(\d*)/m';

            preg_match_all($re, $export, $matches, PREG_SET_ORDER, 0);

            if (count($matches) == 0 || count($matches) == 1 || count($matches) == 2) {
                return ['success' => true, 'message' => 'Process Is Not Running'];
            } elseif (count($matches) == 3 || count($matches) == 4) {
                return ['success' => true, 'message' => 'Process Is Running'];
            }

            return ['success' => true, 'message' => 'Process cannot be check !'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Something went wrong'];
        }
    }

    public function influencer(Request $request)
    {
        $request->posts ? $posts = $request->posts : $posts = null;
        $request->followers ? $followers = $request->followers : $followers = null;
        $request->following ? $following = $request->following : $following = null;
        $request->term ? $term = $request->term : $term = null;
        $influencers = ScrapInfluencer::query();
        if ($posts) {
            $influencers = $influencers->where('posts', '>=', $posts);
        }
        if ($followers) {
            $influencers = $influencers->where('followers', '>=', $followers);
        }
        if ($following) {
            $influencers = $influencers->where('following', '>=', $following);
        }
        if ($term != null) {
            $influencers = $influencers->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('phone', 'LIKE', "%{$term}%")
                    ->orWhere('website', 'LIKE', "%{$term}%")
                    ->orWhere('twitter', 'LIKE', "%{$term}%")
                    ->orWhere('facebook', 'LIKE', "%{$term}%")
                    ->orWhere('country', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }
        $influencers = $influencers->orderBy('created_at', 'desc')->paginate(25);
        $keywords = InfluencerKeyword::all();
        $accounts = Account::where('status', 1)->where('platform', 'instagram')->pluck('last_name', 'id');
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('instagram.hashtags.partials.influencer-data', compact('influencers', 'posts', 'followers', 'following', 'term'))->render(),
                'links' => (string) $influencers->render(),
                'total' => $influencers->total(),
            ], 200);
        }

        $replies = \App\Reply::where('model', 'influencers')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();
        $mailingListTemplates = MailinglistTemplate::pluck('name', 'id')->toArray();

        return view('instagram.hashtags.influencers', compact('accounts', 'replies', 'influencers', 'keywords', 'posts', 'followers', 'mailingListTemplates', 'following', 'term'));
    }

    public function sendMailToInfluencers(Request $request)
    {
        $ids = explode(',', $request->selectedInfluencers);
        foreach ($ids as $id) {
            $customer = ScrapInfluencer::find($id);
            $templateData = MailinglistTemplate::where('id', $request->mailing_list)->first();
            if ($templateData->static_template) {
                $arrToReplace = ['{FIRST_NAME}'];
                $valToReplace = [$customer->name];
                $bodyText = str_replace($arrToReplace, $valToReplace, $templateData->static_template);
            } else {
                $bodyText = @(string) view($templateData->mail_tpl);
            }

            $storeEmailAddress = \App\EmailAddress::first();
            $emailData['subject'] = $templateData->subject;
            $emailData['template'] = $bodyText;
            $emailData['from'] = $storeEmailAddress->from_address;

            $emailClass = (new  \App\Mail\SendInfluencerEmail($emailData))->build();
            $email = \App\Email::create([
                'model_id' => $customer->id,
                'model_type' => \App\ScrapInfluencer::class,
                'from' => $emailClass->fromMailer,
                'to' => $customer->email,
                'subject' => $templateData->subject,
                'message' => $emailClass->render(),
                'template' => 'scrapper-email',
                'additional_data' => '',
                'status' => 'pre-send',
                'is_draft' => 1,
            ]);
            \App\EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Email initiated',
                'message' => $email->to,
            ]);
            \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
        }

        return response()->json(['message' => 'Successfull.'], 200);
    }

    public function addReply(Request $request)
    {
        $reply = $request->get('reply');
        $autoReply = [];
        if (! empty($reply)) {
            $autoReply = \App\Reply::updateOrCreate(
                ['reply' => $reply, 'model' => 'influencers', 'category_id' => 1],
                ['reply' => $reply]
            );
        }

        return response()->json(['code' => 200, 'data' => $autoReply]);
    }

    public function deleteReply(Request $request)
    {
        $id = $request->get('id');

        if ($id > 0) {
            $autoReply = \App\Reply::where('id', $id)->first();
            if ($autoReply) {
                $autoReply->delete();
            }
        }

        return response()->json([
            'code' => 200, 'data' => \App\Reply::where('model', 'influencers')
                ->whereNull('deleted_at')
                ->pluck('reply', 'id')
                ->toArray(),
        ]);
    }

    public function history(Request $request)
    {
        if ($request->id) {
            $history = InfluencersHistory::where('influencers_name', $request->id)->orderBy('created_at', 'desc')->get();

            return response()->json(['code' => 200, 'data' => $history]);
        }
    }

    public function addmailinglist(Request $request)
    {
        $services = \App\Service::first();
        $service_id = $services->id;
        $influencers = \App\ScrapInfluencer::where('email', '!=', '')->get();
        $websites = \App\StoreWebsite::select('id', 'title', 'send_in_blue_api', 'send_in_blue_account')->orderBy('id', 'desc')->get();

        $email_list = [];
        $email_list2 = [];
        $listIds = [];
        foreach ($influencers as $influencer) {
            $email_list[] = ['email' => $influencer->email, 'name' => $influencer->name, 'platform' => $influencer->platform];
        }

        foreach ($websites as $website) {
            $name = $website->title;
            $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : getenv('SEND_IN_BLUE_API');

            if ($name != '') {
                $name = $name . '_' . date('d_m_Y');
            } else {
                $name = 'WELCOME_LIST_' . date('d_m_Y');
            }
            $res = '';

            for ($count = 0; $count < count($email_list); $count++) {
                $email = $email_list[$count]['email'];
                if (! Mailinglist::where('email', $email)->where('website_id', $website->id)->first()) {
                    if (! isset($res->id)) {
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $data = [
                            'folderId' => 1,
                            'name' => $name,
                        ];
                        $url = "'https://api.sendinblue.com/v3/contacts/lists'";
                        $response = Http::withHeaders([
                            'api-key' => $api_key,
                        ])->post($url, $data);
                        $res = $response->json();

                        LogRequest::log($startTime, $url, 'POST', json_encode($data), $res, $response->status(), HashtagController::class, 'addmailinglist');

                        Mailinglist::create([
                            'name' => $name,
                            'website_id' => $website->id,
                            'service_id' => $service_id,
                            'email' => $email,
                            'remote_id' => $res->id,
                            'send_in_blue_api' => $website->send_in_blue_api,
                            'send_in_blue_account' => $website->send_in_blue_account,
                        ]);
                        $listIds[] = $res->id;
                    }
                    if (isset($res->id)) {
                        $email_list2[] = ['email' => $email_list[$count]['email'], 'name' => $email_list[$count]['name']];

                        if (! \App\Customer::where('email', $email)->first()) {
                            $customer = new Customer;

                            $customer->email = $email;
                            $customer->name = $email_list[$count]['name'];
                            $customer->store_website_id = $website->id;
                            $customer->save();
                        }
                    }
                }
            }
        }

        for ($count = 0; $count < count($email_list2); $count++) {
            $email = $email_list2[$count]['email'];
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $data = [
                'email' => $email,
                'listIds' => $listIds,
                'attributes' => ['firstname' => $email_list2[$count]['name']],
            ];
            $url = 'https://api.sendinblue.com/v3/contacts';

            $response = Http::withHeaders([
                'api-key' => env('SEND_IN_BLUE_API'),
            ])->post($url, $data);

            $responseData = $response->json();

            LogRequest::log($startTime, $url, 'POST', json_encode($data), $responseData, $response->status(), HashtagController::class, 'addmailinglist');
        }

        return redirect()->back()->with('message', 'mailinglist create successfully');
    }

    public function loginstance(Request $request)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/get-logs';
        $date = ($request->date != '') ? \Carbon\Carbon::parse($request->date)->format('m-d-Y') : '';
        $id = $request->id;

        if (! empty($date)) {
            $data = ['name' => $id, 'date' => $date];
        } else {
            return response()->json([
                'type' => 'error',
                'response' => 'Please select Date',
            ], 200);
        }

        Log::info('INFLUENCER_loginstance -->' . $data);
        $response = Http::withHeaders([
            'accept' => 'application/json',
        ])->post($url, $data);
        $responseData = $response->json();

        LogRequest::log($startTime, $url, 'POST', json_encode($data), $responseData, $response->status(), HashtagController::class, 'loginstance');

        $result = explode("\n", json_encode($responseData));

        if (count($result) > 1) {
            return response()->json([
                'type' => 'success',
                'response' => view('instagram.hashtags.partials.get_status', compact('result'))->render(),
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'response' => ($result[0] == '') ? 'Please select Date' : "Instagram Scrapter for $id not found",
            ], 200);
        }
    }

    public function changeCronSetting(Request $request)
    {
        $setting = Setting::get('run_mailing_command');
        $value = ($setting == 1) ? 0 : 1;
        $action = ($setting == 1) ? 'Stopped' : 'Started';
        Setting::set('run_mailing_command', $value, $type = 'int');

        return redirect()->back()->with('message', "Maillist command has  $action successfully.");
    }
}
