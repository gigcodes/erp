<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Input;
use App\ReviewBrandList;

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
        foreach ($request as $key => $value) {
            return $value;
        }
        
    }
}
