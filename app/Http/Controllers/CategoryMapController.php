<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryMap;
use App\ScrapedProducts;
use Illuminate\Http\Request;

class CategoryMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        $categories = Category::where('id', '>', '3')->distinct()->get(['title']);
        $scrapProductsCategory = ScrapedProducts::all();
        $filteredCategories = [];

        $maps = CategoryMap::all();

        $mapsToExlude = [];

        foreach ($maps as $map) {
            $mapsToExlude = array_merge($mapsToExlude, $map->alternatives);
        }

        foreach ($scrapProductsCategory as $spc) {
            $category = $spc->properties['category'] ?? [];
            if ($category === []) {
                continue;
            }

            if (is_array($category)) {
                foreach ($category as $item) {
                    if (!in_array($item, $mapsToExlude)) {
                        $filteredCategories[$item] = $item;
                    }
                }

                continue;
            }

            if (!is_array($category) && !in_array($category, $mapsToExlude)) {
                $filteredCategories[$category] = $category;
                continue;
            }

        }

        $items = collect($filteredCategories)->chunk(10);

        return view('category.map', compact('items', 'categories', 'maps'));

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'cats' => 'required|array',
            'category' => 'required'
        ]);

        $cms = CategoryMap::where('title', $request->get('category'))->first();

        if ($cms) {
            $cats = $cms->alternatives;
            $cats = array_merge($cats, $request->get('cats'));
            $cms->alternatives = $cats;
            $cms->save();

            return redirect()->back()->with('message', 'Alternatives merged successfully!');
        }
        $cms = new CategoryMap();
        $cms->title = $request->get('category');
        $cms->alternatives = $request->get('cats');
        $cms->save();

        return redirect()->back()->with('message', 'Alternatives merged successfully!');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CategoryMap  $categoryMap
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryMap $categoryMap)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CategoryMap  $categoryMap
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryMap $categoryMap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoryMap  $categoryMap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryMap $categoryMap)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryMap  $categoryMap
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryMap $categoryMap)
    {
        //
    }
}
