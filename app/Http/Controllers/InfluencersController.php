<?php

namespace App\Http\Controllers;

use DB;
use App\LogRequest;
use App\Influencers;
use App\InfluencersDM;
use App\InfluencerKeyword;
use App\InfluencersHistory;
use Illuminate\Http\Request;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class InfluencersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illum
     * inate\Http\Response
     *
     * List all influencers
     */
    public function index()
    {
        $hashtags = Influencers::all();
        $keywords = InfluencerKeyword::all();
        dd($hastags);

        return view('instagram.influencers.index', compact('hashtags'));
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
     * CReate a new influencer record..
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $i = new Influencers();
        $i->username = $request->get('name');
        $i->brand_name = $request->get('brand_name');
        $i->blogger = $request->get('blogger');
        $i->city = $request->get('city');
        $i->save();

        return redirect()->back()->with('message', 'Added instagram influencer.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comments = InfluencersDM::all();

        return view('instagram.influencers.comments', compact('comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Influencers $influencers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Influencers $influencers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Influencers $influencers)
    {
        //
    }

    public function saveKeyword(Request $request)
    {
        $name = $request->name;

        $keywordCheck = InfluencerKeyword::where('name', $name)->first();

        if (! $keywordCheck) {
            $keyword = new InfluencerKeyword();
            $keyword->name = $name;
            $keyword->instagram_account_id = $request->get('instagram_account_id', null);
            $keyword->save();

            return response()->json(['message' => 'Influencer Keyword Saved']);
        } else {
            $keywordCheck->name = $name;
            $keywordCheck->instagram_account_id = $request->get('instagram_account_id', null);
            $keywordCheck->save();

            return response()->json(['message' => 'Influencer Keyword Saved']);
        }

        return response()->json(['message' => 'Influencer Keyword Exist']);
    }

    public function getScraperImage(Request $request)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $name = $request->name;
        $extraVars = \App\Helpers::getInstagramVars($name);
        $name = str_replace(' ', '', $name) . $extraVars;

        $cURLConnection = curl_init();

        $url = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/get-image?' . $name;

        curl_setopt($cURLConnection, CURLOPT_URL, $url);

        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'getScraperImage');
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);

        $b64 = $jsonArrayResponse->status;

        $history = [
            'influencers_name' => $name,
            'title' => 'Getting image',
            'description' => $b64,
        ];
        InfluencersHistory::insert($history);

        if ($jsonArrayResponse->status == 'Something Went Wrong') {
            return \Response::json(['success' => false, 'message' => 'No Image Available']);
        }
        $content = base64_decode($b64);

        $media = MediaUploader::fromString($content)->toDirectory('/influencer')->useFilename($name)->upload();

        return \Response::json(['success' => true, 'message' => getMediaUrl($media)]);
    }

    public function checkScraper(Request $request)
    {
        try {
            $name = $request->name;

            // get keyword name
            $extraVars = \App\Helpers::getInstagramVars($name);
            $name = str_replace(' ', '', $name) . $extraVars;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $cURLConnection = curl_init();
            $url = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/get-status';
            $data = json_encode(['name' => $name]);
            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
            $phoneList = curl_exec($cURLConnection);
            $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'checkScraper');
            curl_close($cURLConnection);
            $jsonArrayResponse = json_decode($phoneList);
            if (isset($jsonArrayResponse->status)) {
                $b64 = $jsonArrayResponse->status;
            } else {
                $b64 = $phoneList;
            }

            $history = [
                'influencers_name' => $name,
                'title' => 'Check status',
                'description' => $b64,
            ];
            InfluencersHistory::insert($history);

            return \Response::json(['success' => true, 'message' => $b64]);
        } catch (\Throwable $th) {
            $history = [
                'influencers_name' => $request->name,
                'title' => 'Check status',
                'description' => $th->getMessage() . env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT'),
            ];
            InfluencersHistory::insert($history);
        }
    }

    public function startScraper(Request $request)
    {
        try {
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $cURLConnection = curl_init();

            $infKeyword = \App\InfluencerKeyword::where('name', $request->name)->first();
            if ($infKeyword) {
                $infKeyword->wait_time = $request->get('wait_time', 0);
                $infKeyword->no_of_requets = $request->get('no_of_request', 0);
                $infKeyword->save();
            }

            if ($request->platform == 'py_facebook') {
                $extraVars = \App\Helpers::getFacebookVars($request->name);
                $url = config('constants.py_facebook_script') . '/fb-keyword-start' . $extraVars;
                $params = [
                    'brand' => str_replace(' ', '', $request->name),
                    'wait_time' => $request->get('wait_time', 0),
                    'num_requests' => $request->get('no_of_request', 0),
                ];
            } else {
                $url = env('INFLUENCER_PY_SCRIPT_URL') . ':' . env('INFLUENCER_PY_SCRIPT_PORT') . '/influencer-keyword-start';
                $params = [
                    'name' => str_replace(' ', '', $request->name),
                    'wait_time' => $request->get('wait_time', 0),
                    'num_requests' => $request->get('no_of_request', 0),
                ];
            }

            curl_setopt_array($cURLConnection, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                ],
            ]);

            $phoneList = curl_exec($cURLConnection);

            \Log::info('Influencers start scraper : ' . $url . ' with params : ' . json_encode($params) . ' and response return ' . (string) $phoneList);
            $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($params), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'startScraper');
            curl_close($cURLConnection);

            $b64 = (string) $phoneList;

            $history = [
                'influencers_name' => $request->name,
                'title' => 'starting script',
                'description' => $b64,
            ];
            InfluencersHistory::insert($history);

            return \Response::json(['success' => true, 'message' => $b64]);
        } catch (\Throwable $th) {
            $history = [
                'influencers_name' => $request->name,
                'title' => 'starting script',
                'description' => $th->getMessage() . env('INFLUENCER_PY_SCRIPT_URL') . ':' . env('INFLUENCER_PY_SCRIPT_PORT'),
            ];
            InfluencersHistory::insert($history);
        }
    }

    public function getLogFile(Request $request)
    {
        try {
            $name = $request->name;
            $extraVars = \App\Helpers::getInstagramVars($name);
            $name = str_replace(' ', '', $name) . $extraVars;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $cURLConnection = curl_init();

            $url = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/send-log?' . $name;
            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $phoneList = curl_exec($cURLConnection);
            $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
            $parameters = [
                'name' => $name,
            ];
            LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'getLogFile');

            curl_close($cURLConnection);

            $jsonArrayResponse = json_decode($phoneList);

            $b64 = $jsonArrayResponse->status;

            if ($jsonArrayResponse->status == 'Something Went Wrong') {
                return \Response::json(['success' => false, 'message' => 'No Logs Available']);
            }
            $content = base64_decode($b64);

            $history = [
                'influencers_name' => $name,
                'title' => 'Getting log file',
                'description' => $b64,
            ];
            InfluencersHistory::insert($history);

            $media = MediaUploader::fromString($content)->toDirectory('/influencer')->useFilename($name)->upload();

            return \Response::json(['success' => true, 'message' => getMediaUrl($media)]);
        } catch (\Throwable $th) {
            $history = [
                'influencers_name' => $request->name,
                'title' => 'Getting log file',
                'description' => $th->getMessage() . env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT'),
            ];
            InfluencersHistory::insert($history);
        }
    }

    public function restartScript(Request $request)
    {
        try {
            $name = $request->name;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $cURLConnection = curl_init();

            $url = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/restart';
            $data = json_encode(['name' => $name]);
            \Log::info('INFLUENCER_loginstance -->' . $data);
            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
            $phoneList = curl_exec($cURLConnection);
            $jsonArrayResponse = json_decode($phoneList);
            if (isset($jsonArrayResponse->status)) {
                $b64 = $jsonArrayResponse->status;
            } else {
                $b64 = $phoneList;
            }

            $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'restartScript');
            curl_close($cURLConnection);

            $history = [
                'influencers_name' => $name,
                'title' => 'Restart script',
                'description' => $b64,
            ];
            InfluencersHistory::insert($history);

            return \Response::json(['success' => true, 'message' => $b64]);
        } catch (\Throwable $th) {
            dd('Sssss');
            $history = [
                'influencers_name' => $request->name,
                'title' => 'Restart script',
                'description' => $th->getMessage() . env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT'),
            ];
            InfluencersHistory::insert($history);
        }
    }

    public function stopScript(Request $request)
    {
        try {
            $name = $request->name;
            $extraVars = \App\Helpers::getInstagramVars($name);
            $name = str_replace(' ', '', $name) . $extraVars;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $cURLConnection = curl_init();
            if ($request->platform == 'py_facebook') {
                $extraVars = \App\Helpers::getInstagramVars($name);
                $url = config('constants.py_facebook_script') . '/fb-keyword-stop' . $extraVars;
                $params = [
                    'brand' => str_replace(' ', '', $request->name),
                ];
            } else {
                $url = env('INFLUENCER_PY_SCRIPT_URL') . ':' . env('INFLUENCER_PY_SCRIPT_PORT') . '/influencer-keyword-stop';
                $params = [
                    'name' => str_replace(' ', '', $request->name),
                ];
            }
            curl_setopt_array($cURLConnection, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                ],
            ]);

            $phoneList = curl_exec($cURLConnection);
            $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($params), json_decode($phoneList), $httpcode, \App\Http\Controllers\InfluencersController::class, 'addmailinglist');

            \Log::info('Influencers stop scraper : ' . $url . ' with params : ' . json_encode($params) . ' and response return ' . (string) $phoneList);

            curl_close($cURLConnection);

            $b64 = (string) $phoneList;

            $history = [
                'influencers_name' => $name,
                'title' => 'Stop script',
                'description' => $b64,
            ];
            InfluencersHistory::insert($history);

            return \Response::json(['success' => true, 'message' => $b64]);
        } catch (\Throwable $th) {
            $history = [
                'influencers_name' => $request->name,
                'title' => 'Stop script',
                'description' => $th->getMessage() . env('INFLUENCER_PY_SCRIPT_URL') . ':' . env('INFLUENCER_PY_SCRIPT_PORT'),
            ];
            InfluencersHistory::insert($history);
        }
    }

    public function sortData()
    {
        try {
            \Artisan::call('influencer:description');

            return response()->json('Console Commnad Ran', 200);
        } catch (\Exception $e) {
            return response()->json('Cannot call artisan command', 200);
        }
    }

    public function getKeywordsWithAccount()
    {
        $getKeywords = InfluencerKeyword::all();
        $data = [];
        foreach ($getKeywords as $key => $value) {
            $account = DB::table('accounts')->where('id', $value->instagram_account_id)->get();
            $datas['keyword'] = $value->name;
            $datas['account'] = $account;
            array_push($data, $datas);
        }

        return $data;
    }
}
