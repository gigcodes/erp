<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\ReviewBrandList;
use Illuminate\Support\Facades\DB;

class BrandReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('brand-review.index');
    }

    public function store(Request $request){
        ReviewBrandList::insert([
            'name' => $request->name
        ]);
    }
    public function getAllBrandReview(){
        $data = ReviewBrandList::all();
        return $data;
    }
    public function storeReview(Request $request){
        $data = Input::all();
        if($data){
            foreach ($data as $key => $value) {
                DB::table('brand_reviews')->insert([
                    'website' => $value['website'],
                    'brand' => $value['brand'],
                    'review_url' => $value['review_url'],
                    'username' => $value['username'],
                    'title' => $value['title'],
                    'body' => $value['body'],
                    'stars' => $value['stars']
                ]);
            }
            return response()->json([
                "code" => 200,
                "message" => 'Data have been updated successfully',
            ]);
        }
        return response()->json([
            "code" => 500,
            "message" => 'Error Occured, please try again later.',
        ]);
    }
}
