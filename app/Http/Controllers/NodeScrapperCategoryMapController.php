<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Models\NodeScrapperCategoryMap;
use App\Http\Requests\StoreNodeScrapperCategoryMapRequest;
use App\Http\Requests\UpdateNodeScrapperCategoryMapRequest;

class NodeScrapperCategoryMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Display data
        $unmapped_categories = NodeScrapperCategoryMap::all()->sortByDesc('id');

        $existing_categories = Category::with([
            'childsOrderByTitle' => function ($query) {
                $query->with([
                    'childsOrderByTitle' => function ($query) {
                        $query->with([
                            'childsOrderByTitle' => function ($query) {
                                $query->with('childsOrderByTitle'); // Add more levels as needed
                            },
                        ]);
                    },
                ]);
            },
        ])
            ->where('parent_id', 0)
            ->orderBy('title')
            ->paginate(1);

        $category_array = [];

        foreach ($existing_categories as $key => $cat) {
            $category_array[$key] = [
                'id'   => $cat->id,
                'name' => $cat->title,
            ];

            foreach ($cat->childsOrderByTitle as $key1 => $firstChild) {
                $category_array[$key]['child'][$key1] = [
                    'id'   => $firstChild->id,
                    'name' => $firstChild->title,
                ];

                if ($firstChild->childsOrderByTitle->count()) {
                    foreach ($firstChild->childsOrderByTitle as $key2 => $secondChild) {
                        $category_array[$key]['child'][$key1]['child'][$key2] = [
                            'id'   => $secondChild->id,
                            'name' => $secondChild->title,
                        ];

                        if ($secondChild->childsOrderByTitle->count()) {
                            foreach ($secondChild->childsOrderByTitle as $key3 => $thirdChild) {
                                $category_array[$key]['child'][$key1]['child'][$key2]['child'][$key3] = [
                                    'id'   => $thirdChild->id,
                                    'name' => $thirdChild->title,
                                ];
                            }
                        }
                    }
                }
            }
        }
        $title = 'Map Category';

        return view('node-category-map.index', compact('unmapped_categories', 'category_array', 'title'));
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
     */
    public function store(StoreNodeScrapperCategoryMapRequest $request)
    {
        try {
            $nodeScrapper                 = new NodeScrapperCategoryMap();
            $nodeScrapper->category_stack = $request->category_stack;
            $nodeScrapper->product_urls   = $request->product_urls;
            $nodeScrapper->supplier       = $request->supplier;
            $nodeScrapper->save();

            return response()->json(['message' => 'Category stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNodeScrapperCategoryMapRequest $request, NodeScrapperCategoryMap $scrapperCategoryMap)
    {
        if ($request->action == 'assign_category') {
            $scrapperCategoryMap->mapped_categories = json_decode($request->mapped_categories) ? json_decode($request->mapped_categories) : null;
            $scrapperCategoryMap->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(NodeScrapperCategoryMap $nodeScrapperCategoryMap)
    {
        //
    }

    public function list(Request $request)
    {
        $unmapped_categories = NodeScrapperCategoryMap::all()->whereNotNull('mapped_categories')->sortByDesc('id');
        $retun_array         = [];
        foreach ($unmapped_categories as $unmapped_category) {
            $disp_cat = $unmapped_category->categories();
            if ($disp_cat) {
                $retun_array[] = [
                    'mapped_category' => $disp_cat,
                    'category_stack'  => $unmapped_category->category_stack,
                    'product_urls'    => $unmapped_category->product_urls,
                    'supplier'        => $unmapped_category->supplier,

                ];
            }
        }

        return response()->json(['status' => true, 'data' => $retun_array], 200);
    }

    public function getRecord(Request $request)
    {
        $request->validate([
            'category_stack' => 'required|array',
        ]);
        $unmapped_category = NodeScrapperCategoryMap::whereJsonContains('category_stack', ($request->category_stack))->latest('updated_at')->first();
        if ($unmapped_category) {
            $retun_array = [
                'mapped_category' => [],
                'category_stack'  => $unmapped_category->category_stack,
                'product_urls'    => $unmapped_category->product_urls,
                'supplier'        => $unmapped_category->supplier,

            ];
            $disp_cat = $unmapped_category->categories();
            if ($disp_cat) {
                $retun_array['mapped_category'] = $disp_cat;
            }

            return response()->json(['status' => true, 'data' => $retun_array], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Category Stack not found'], 404);
        }
    }
}
