<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\HashTag;
use App\InstagramPosts;
use App\KeywordSearchVariants;
use App\Library\Watson\Response;
use App\Setting;
use Google\Service\AndroidPublisher\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//use App\InstagramPosts;

class GoogleSearchController extends Controller
{
    public $platformsId;

    public function __construct(Request $request)
    {
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

        if ($request->term || $request->priority) {
            if ($request->term != null && $request->priority == 'on') {
                $keywords = HashTag::query()
                    ->where('priority', '1')
                    ->where('platforms_id', $this->platformsId)
                    ->where('hashtag', 'LIKE', "%{$request->term}%")
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(Setting::get('pagination'));

                $queryString = 'term=' . $request->term . '&priority=' . $request->priority . '&';
            } elseif ($request->priority == 'on') {
                $keywords = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));

                $queryString = 'priority=' . $request->priority . '&';
            } elseif ($request->term != null) {
                $keywords = HashTag::query()
                    ->where('hashtag', 'LIKE', "%{$request->term}%")
                    ->where('platforms_id', $this->platformsId)
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(Setting::get('pagination'));

                $queryString = 'term=' . $request->term . '&';
            }
        } else {
            $keywords = HashTag::where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));
        }

        $brandList = Brand::list();
        $new_category_selection = Category::attr(['name' => 'category', 'class' => 'form-control', 'id' => 'product-category'])->renderAsDropdown();

        $variants = KeywordSearchVariants::list();
        return view('google.search.index', compact('keywords', 'queryString', 'orderBy', 'brandList', 'variants', 'new_category_selection'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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
     * @param \Illuminate\Http\Request $request
     * @return json response with status
     */
    public function markPriority(Request $request)
    {
        $id = $request->id;
        //check if 30 limit is exceded
        $hashtags = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->get();

        if (count($hashtags) >= 30 && $request->type == 1) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $hashtag = HashTag::findOrFail($id);
        $hashtag->priority = $request->type;
        $hashtag->update();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * @SWG\Get(
     *   path="/google/keywords",
     *   tags={"Google"},
     *   summary="Get google keywords",
     *   operationId="get-google-keywords",
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

    /**
     * function to get keywords to api
     *
     * @return json response with keywords
     */
    public function getKeywordsApi()
    {
        $keywords = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->get(['hashtag', 'id']);

        return response()->json($keywords);
    }

    /**
     * @SWG\Post(
     *   path="/google/search-results",
     *   tags={"Google"},
     *   summary="post google search result",
     *   operationId="post-google-search-result",
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
     * @param \Illuminate\Http\Request $request
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
        } else {
            $postedData = $payLoad['json'];
            // Loop over posts
            foreach ($postedData as $postJson) {
                // Set tag
                $tag = $postJson['searchKeyword'];

                // Get hashtag ID
                //$hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);

                $keywords = HashTag::query()
                    ->where('hashtag', 'LIKE', $tag)
                    ->where('platforms_id', $this->platformsId)->first();

                if (is_null($keywords)) {
                    //keyword not in DB. For now skip this...
                } else {
                    // Retrieve instagram post or initiate new
                    $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson['URL']]);

                    $instagramPost->hashtag_id = $keywords->id;
                    $instagramPost->caption = $postJson['description'];
                    $instagramPost->posted_at = ($postJson['crawledAt']) ? date('Y-m-d H:i:s', strtotime($postJson['crawledAt'])) : date('Y-m-d H:i:s');
                    $instagramPost->media_type = 'other';
                    $instagramPost->media_url = $postJson['URL'];
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

    /**
     * function to get google search results
     *
     * @param \Illuminate\Http\Request $request
     * @return data to view
     */
    public function searchResults(Request $request)
    {
        $queryString = '';
        $orderBy = 'DESC';
        if (!empty($request->hashtag)) {
            $queryString .= 'hashtag=' . $request->hashtag . '&';
        }
        if (!empty($request->location)) {
            $queryString .= 'location=' . $request->location . '&';
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
                'tbody' => view('google.search.row_results', compact('posts'))->render(),
                'links' => (string)$posts->appends($request->all())->render(),
            ], 200);
        }

        // Return view
                return view('google.search.results', compact('posts', 'request', 'queryString', 'orderBy'));
    }

    /**
     * function to get google search results
     *
     * @param \Illuminate\Http\Request $request
     * @return array of search results
     */
    private function getFilteredGoogleSearchResults(Request $request)
    {
        $sortBy = ($request->input('sortby') == '') ? 'posted_at' : $request->input('sortby');
        $orderBy = ($request->input('orderby') == '') ? 'DESC' : $request->input('orderby');

        // Base query
        $instagramPosts = InstagramPosts::orderBy($sortBy, $orderBy)
            ->join('hash_tags', 'instagram_posts.hashtag_id', '=', 'hash_tags.id')
            ->select(['instagram_posts.*', 'hash_tags.hashtag']);

        //Pick google search result from DB
        $instagramPosts->where('source', '=', 'google');

        // Apply hashtag filter
        if (!empty($request->hashtag)) {
            $instagramPosts->where('hash_tags.hashtag', $request->hashtag);
        }

        // Apply location filter
        if (!empty($request->location)) {
            $instagramPosts->where('location', 'LIKE', '%' . $request->location . '%');
        }

        // Apply post filter
        if (!empty($request->post)) {
            $instagramPosts->where('caption', 'LIKE', '%' . $request->post . '%');
        }

        // Apply posted at filter
        if (!empty($request->date)) {
            $instagramPosts->where('posted_at', date('Y-m-d H:i:s', strtotime($request->date)));
        }

        // Return google search results
        return $instagramPosts;
    }

    /**
     * function to call google scraper
     *
     * @param \Illuminate\Http\Request $request , id of keyword to scrap
     * @return \Illuminate\Http\JsonResponse, failure
     */
    public function callScraper(Request $request)
    {
        $id = $request->input('id');

        $searchKeywords = HashTag::where('id', $id)->get(['hashtag', 'id']);
        if (is_null($searchKeywords)) {
            // Return
            return response()->json([
                'error' => 'Keyword not found',
            ], 400);
        } else {
            $postData = [];
            $postData['data'] = $searchKeywords;
            $postData = json_encode($postData);

            // call this endpoint - /api/googleSearch
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => env('NODE_SCRAPER_SERVER') . 'api/googleSearch',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 50,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => $postData,
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $result = null;
            if(!empty($response)) {
                $result = json_decode($response);
            }
            if (empty($err) && !empty($result) && $result->error) {
                $err = $result->message;
            }

            return response()->json(['data' => $result, 'error' => $err], 200);
        }
    }

    /**
     * Function for auto generate Keywords from brand, category and sub category
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateKeywords(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $brand_list = Brand::select('name', 'id')->get()->toArray();
        $category_list_with_variant = Category::getCategoryHierarchySting();
        return response()->json(['success' => true, 'data' => $category_list_with_variant], 200);
        // Define the arrays
        $array1 = $brand_list;  // array with 8000 records
        $array2 = $category_list_with_variant; // array with 400 records

        // Define the chunk size for each array
        $chunkSize1 = 300;
        $chunkSize2 = 100;

        // Loop over the chunks of the first array
        for ($i = 0; $i < count($array1); $i += $chunkSize1) {
            $chunk1 = array_slice($array1, $i, $chunkSize1);

            // Loop over the chunks of the second array
            for ($j = 0; $j < count($array2); $j += $chunkSize2) {
                $chunk2 = array_slice($array2, $j, $chunkSize2);
                $string_arr = [];
                // Loop over the combinations   of the current chunks
                foreach ($chunk1 as $value1) {
                    foreach ($chunk2 as $value2) {
                        $string_arr['hashtag'] = $value1['name'] . ' ' . $value2->combined_string;
                        $string_arr['platforms_id'] = $this->platformsId;
                        $string_arr['rating'] = 8;
                        $string_arr['created_at'] = $string_arr['updated_at'] = date('Y-m-d h:i:s');
                        $insertA_arr[] = $string_arr;
                    }
                    if(count($insertA_arr) > 0) {
                        HashTag::insertOrIgnore($insertA_arr);
                    }
                }
            }
        }
        return response()->json(['success' => true], 200);

    }

    /*public function deleteSearch($id)
    {
      $instaPost = InstagramPosts::find($id);

      if($instaPost){
        $instaPost->delete();
      }

      return response()->json(['message' => "delete successfully"]);

    }*/
}
