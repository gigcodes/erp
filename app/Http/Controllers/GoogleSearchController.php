<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HashTag;
use App\Setting;
use App\InstagramPosts;

class GoogleSearchController extends Controller
{
	public $platformsId;

	public function __construct(Request $request){
        $this->platformsId = 2;
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
    	$platformsId = 2;
        $queryString = '';
        $sortBy = 'hashtag';
        if ($request->input('orderby') == '') {
            $orderBy = 'DESC';
        } else {
            $orderBy = 'ASC';
        }

        /*if ($request->input('sortby') == '') {
            $sortBy = 'hashtag';
        } else {
            $sortBy = '';
        }*/

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

		return view('google.search.index', compact('keywords', 'queryString', 'orderBy')); 
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
    * JSON data posted to this api will be array of objects in below format 
    * [
    *    {  "searchKeyword": "GKy1", 
    *    "description": "This is description about web page in search result", 
    *    "crawledAt": "2019-01-10", 
    *    "URL": "http://www.searchedweb1.com" },
    *    { "searchKeyword": "GKy2", 
    *    "description": "This is description about web page in search result", 
    *    "crawledAt": "2019-01-10", 
    *    "URL": "http://www.searchedweb2.com" }
    * ]
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
                //$hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);

                $keywords = HashTag::query()
                                ->where('hashtag', 'LIKE', $tag)
                                ->where('platforms_id', $this->platformsId)->first();

                if (is_null($keywords)){
                    //keyword not in DB. For now skip this...
                }
                else {
                    // Retrieve instagram post or initiate new
                    $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson[ 'URL' ]]);

                    $instagramPost->hashtag_id = $keywords->id;
                    $instagramPost->caption = $postJson[ 'description' ];
                    $instagramPost->posted_at = ($postJson[ 'crawledAt' ]) ? date('Y-m-d H:i:s', strtotime($postJson[ 'crawledAt' ])) : date('Y-m-d H:i:s');
                    $instagramPost->media_type = 'other';
                    $instagramPost->media_url = $postJson[ 'URL' ];
                    $instagramPost->source = 'google';
                    $instagramPost->save();                    
                }
            }
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }      
}
