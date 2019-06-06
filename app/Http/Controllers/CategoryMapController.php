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

        foreach ($scrapProductsCategory as $spc) {
            $category = $spc->properties['category'] ?? [];
            if ($category === []) {
                continue;
            }

            if (!is_array($category)) {
                $filteredCategories[$category] = $category;
                continue;
            }

            foreach ($category as $item) {
                $filteredCategories[$item] = $item;
            }

        }

        $items = collect($filteredCategories)->chunk(10);

        return view('category.map', compact('items', 'categories'));

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
        //
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
