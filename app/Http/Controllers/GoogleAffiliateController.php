<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HashTag;
use App\Setting;
use App\Affiliates;
use App\Mail\AffiliateEmail;
use Illuminate\Support\Facades\Mail;

class GoogleAffiliateController extends Controller
{
	public $platformsId;

	public function __construct(Request $request){
        $this->platformsId = 3;
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
        $queryString = '';
        $sortBy = 'hashtag';
        if ($request->input('orderby') == '') {
            $orderBy = 'DESC';
        } else {
            $orderBy = 'ASC';
        }

		if($request->term || $request->priority ){

			if($request->term != null && $request->priority == 'on'){
                
				$keywords  = HashTag::query()
                                ->where('priority', '1')
								->where('platforms_id', $this->platformsId)
								->where('hashtag', 'LIKE', "%{$request->term}%")
                                ->orderBy($sortBy, $orderBy)
								->paginate(Setting::get('pagination'));

                $queryString = 'term=' . $request->term . '&priority=' . $request->priority . '&';
			}
			else if($request->priority == 'on'){
				$keywords = HashTag::where('priority',1)->where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));
            
                $queryString = 'priority=' . $request->priority . '&';
			}
			else if($request->term != null){
				$keywords  = HashTag::query()
								->where('hashtag', 'LIKE', "%{$request->term}%")
								->where('platforms_id', $this->platformsId)
                                ->orderBy($sortBy, $orderBy)
								->paginate(Setting::get('pagination'));
                
                $queryString = 'term=' . $request->term . '&';
			}

		} else {
			$keywords = HashTag::where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));
		}

		return view('google.affiliate.index', compact('keywords', 'queryString', 'orderBy')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * Create a new hashtag entry
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $hashtag = new HashTag();
        $hashtag->hashtag = $request->get('name');
        $hashtag->rating = $request->get('rating') ?? 8;
        $hashtag->platforms_id = $this->platformsId;
        $hashtag->save();

        return redirect()->back()->with('message', 'Keyword created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        if (is_numeric($id)) {
            $hash = HashTag::findOrFail($id);
            $hash->delete();
        } else {
            HashTag::where('hashtag', $id)->delete();
        }

        return redirect()->back()->with('message', 'Keyword has been deleted successfuly!');
    }

    /**
    * function to set priority for keywords
    *
    * @param  \Illuminate\Http\Request  $request
    * @return json response with status
    */
    public function markPriority(Request $request)
    {
        $id = $request->id;
        //check if 30 limit is exceded
        $hashtags = HashTag::where('priority',1)->where('platforms_id', $this->platformsId)->get();

        if(count($hashtags) >= 30 && $request->type == 1){
             return response()->json([
            'status' => 'error'
            ]);
        }

        $hashtag = HashTag::findOrFail($id);
        $hashtag->priority = $request->type;
        $hashtag->update(); 
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
    * function to get keywords to api
    *
    * @return json response with keywords
    */
    public function getKeywordsApi() {
        $keywords = HashTag::where('priority',1)->where('platforms_id', $this->platformsId)->get(['hashtag', 'id']);

        return response()->json($keywords);
    }

    /**
    * function to store google search results sent from scrapper
    *
    * @param  \Illuminate\Http\Request  $request
    * @return json response status
    */
    public function apiPost(Request $request)
    {
        // Get raw body        
        $payLoad = $request->all();

        $payLoad = json_decode(json_encode($payLoad), true);

        // Process input
        if (count($payLoad) == 0) {
            return response()->json([
                'error' => 'Invalid json'
            ], 400);
        }
        else {
            // Loop over posts
            foreach ($payLoad as $postJson) {
                // Set tag
                $tag = $postJson[ 'searchKeyword' ];

                // Get hashtag ID
                $keywords = HashTag::query()
                                ->where('hashtag', 'LIKE', $tag)
                                ->where('platforms_id', $this->platformsId)->first();

                if (is_null($keywords)){
                    //keyword not in DB. For now skip this...
                }
                else {
                    // Retrieve instagram post or initiate new
                    $affiliateResults = Affiliates::firstOrNew(['location' => $postJson[ 'URL' ]]);

                    $affiliateResults->hashtag_id = $keywords->id;
                    $affiliateResults->title = $postJson[ 'title' ];
                    $affiliateResults->caption = $postJson[ 'description' ];
                    $affiliateResults->posted_at = ($postJson[ 'crawledAt' ]) ? date('Y-m-d H:i:s', strtotime($postJson[ 'crawledAt' ])) : date('Y-m-d H:i:s');
                    $affiliateResults->address = $postJson[ 'address' ];
                    $affiliateResults->facebook = $postJson[ 'facebook' ];
                    $affiliateResults->instagram = $postJson[ 'instagram' ];
                    $affiliateResults->twitter = $postJson[ 'twitter' ];
                    $affiliateResults->youtube = $postJson[ 'youtube' ];
                    $affiliateResults->linkedin = $postJson[ 'linkedin' ];
                    $affiliateResults->pinterest = $postJson[ 'pinterest' ];
                    $affiliateResults->phone = $postJson[ 'phone' ];
                    $affiliateResults->emailaddress = $postJson[ 'emailaddress' ];
                    $affiliateResults->source = 'google';
                    $affiliateResults->save();                    
                }
            }
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }

    /**
    * function to get google affiliate search results
    *
    * @param  \Illuminate\Http\Request  $request
    * @return data to view
    */
    public function searchResults(Request $request)
    {
        $queryString = '';
        $orderBy = 'DESC';
        if (!empty($request->hashtag)) {
            $queryString .= 'hashtag=' . $request->hashtag . '&';
        }
        if (!empty($request->title)) {
            $queryString .= 'title=' . $request->title . '&';
        }
        if (!empty($request->post)) {
            $queryString .= 'post=' . $request->post . '&';
        }
        if (!empty($request->date)) {
            $queryString .= 'date=' . $request->date . '&';
        }
        if (!empty($request->orderby)) {
            $orderBy = $request->orderby;
        }

        // Load posts
        $posts = $this->getFilteredGoogleSearchResults($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google.affiliate.row_results', compact('posts'))->render(),
                'links' => (string)$posts->appends($request->all())->render()
            ], 200);
        }

        // Return view
        return view('google.affiliate.results', compact('posts', 'request', 'queryString', 'orderBy'));
    }

    /**
    * function to get google affiliate search results
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array of search results
    */
    private function getFilteredGoogleSearchResults(Request $request) {
        $sortBy = ($request->input('sortby') == '') ? 'posted_at' : $request->input('sortby');
        $orderBy = ($request->input('orderby') == '') ? 'DESC' : $request->input('orderby');

        // Base query
        $affiliateResults = Affiliates::orderBy($sortBy, $orderBy)
            ->join('hash_tags', 'affiliates.hashtag_id', '=', 'hash_tags.id')
            ->select(['affiliates.*','hash_tags.hashtag']);

        //Pick google search result from DB
        $affiliateResults->where('source', '=', 'google');

        // Apply hashtag filter
        if (!empty($request->hashtag)) {
            $affiliateResults->where('hash_tags.hashtag', $request->hashtag);
        }

        // Apply location filter
        if (!empty($request->title)) {
            $affiliateResults->where('title', 'LIKE', '%' . $request->title . '%');
        }

        // Apply post filter
        if (!empty($request->post)) {
            $affiliateResults->where('caption', 'LIKE', '%' . $request->post . '%');
        }

        // Apply posted at filter
        if (!empty($request->date)) {
            $affiliateResults->where('posted_at', date('Y-m-d H:i:s', strtotime($request->date)));
        }

        // Return google search results
        return $affiliateResults;
    }

    public function flag(Request $request)
    {
        $affiliates = Affiliates::find($request->id);

        if ($affiliates->is_flagged == 0) {
            $affiliates->is_flagged = 1;
        } else {
            $affiliates->is_flagged = 0;
        }

        $affiliates->save();

        return response()->json(['is_flagged' => $affiliates->is_flagged]);
    }

    public function emailSend(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required'
        ]);
        \Log::info($request);
        $affiliates = Affiliates::find($request->affiliate_id);
        \Log::info($affiliates);
        Mail::to($affiliates->emailaddress)->send(new AffiliateEmail($request->subject, $request->message));


        $params = [
            'model_id' => $affiliates->id,
            'model_type' => Affiliates::class,
            'from' => 'affiliate@amourint.com',
            'to' => $affiliates->emailaddress,
            'send' => 1,
            'subject' => $request->subject,
            'message' => $request->message,
            'template' => 'customer-simple',
            'additional_data' => ''
        ];

        Email::create($params);

        return redirect()->route('affiliates.index', $customer->id)->withSuccess('You have successfully sent an email!');
    }
}
