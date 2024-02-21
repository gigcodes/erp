<?php

namespace App\Http\Controllers;

use App\Models\NodeScrapperCategoryMap;
use App\Http\Requests\StoreNodeScrapperCategoryMapRequest;
use App\Http\Requests\UpdateNodeScrapperCategoryMapRequest;
use Illuminate\Http\Request;

class NodeScrapperCategoryMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreNodeScrapperCategoryMapRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNodeScrapperCategoryMapRequest $request)
    {
        
        try {
            
        $nodeScrapper = new NodeScrapperCategoryMap();
        $nodeScrapper->category_stack = json_encode($request->category_stack) ;
        $nodeScrapper->product_urls = json_encode($request->product_urls);
        $nodeScrapper->supplier = $request->supplier;
        $nodeScrapper->save();
        return response()->json(['message' => 'Category stored successfully'], 200); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NodeScrapperCategoryMap  $nodeScrapperCategoryMap
     * @return \Illuminate\Http\Response
     */
    public function show(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NodeScrapperCategoryMap  $nodeScrapperCategoryMap
     * @return \Illuminate\Http\Response
     */
    public function edit(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNodeScrapperCategoryMapRequest  $request
     * @param  \App\Models\NodeScrapperCategoryMap  $nodeScrapperCategoryMap
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNodeScrapperCategoryMapRequest $request, NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NodeScrapperCategoryMap  $nodeScrapperCategoryMap
     * @return \Illuminate\Http\Response
     */
    public function destroy(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }
}
