<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\StoreWebsite;
use Illuminate\Http\Request;
use App\BrandCategorySizeChart;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class BrandSizeChartController extends Controller
{
    //

    public function __construct()
    {
    }

    public function index()
    {
        $storeWebsite = \App\StoreWebsite::get();
        if ($storeWebsite) {
            foreach ($storeWebsite as $k => $_website) {
                $size = BrandCategorySizeChart::where('store_website_id', $_website->id)->with('brands')->get()->toArray();
                $storeWebsite[$k]['size_charts'] = $size;
            }
        }

        $sizeChart = BrandCategorySizeChart::get();

        return view('brand-size-chart.index', compact('storeWebsite', 'sizeChart'));
    }

    /**
     * Create size chart
     */
    public function createSizeChart()
    {
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $category = Category::where('parent_id', 0)->orderBy('title', 'asc')->pluck('title', 'id');
        $storeWebsite = StoreWebsite::orderBy('website', 'asc')->pluck('website', 'id');

        return view('brand-size-chart.create', ['brands' => $brands, 'category' => $category, 'storeWebsite' => $storeWebsite]);
    }

    /**
     * Store brand size chart
     */
    public function storeSizeChart(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'size_img' => 'required|mimes:jpeg,jpg,png',
        ]);
        $brandCat = BrandCategorySizeChart::create([
            'brand_id' => $request->brand_id ?? 0,
            'category_id' => $request->category_id,
            'store_website_id' => $request->store_website_id,
        ]);

        if ($request->hasfile('size_img')) {
            $media = MediaUploader::fromSource($request->file('size_img'))
                ->toDirectory('brand-size-chart')
                ->upload();
            $brandCat->attachMedia($media, ['size_chart']);
        }

        session()->flash('success', 'Brand size chart uploaded successfully');

        return redirect()->route('brand/size/chart');
    }

    /**
     * Ajax to get Child of main category
     */
    public function getChild(Request $request)
    {
        if ($request->ajax()) {
            $categoryId = $request->category_id;
            $data = Category::find($categoryId)->toArray();
            $categories = Category::where('parent_id', $categoryId)->orderBy('title')->get();
            $tableBody = "<tr><td class='text-center'><input type='radio' id='category_id' name='category_id' value='" . $data['id'] . "' required> </td><td>" . $data['title'] . '</td></tr>';
            if ($categories) {
                foreach ($categories as $category) {
                    $data1 = $category->toArray();
                    $tableBody .= "<tr><td class='text-center'><input type='radio' id='category_id' name='category_id' value='" . $data1['id'] . "' required> </td><td style='padding-left:30px'>" . $data1['title'] . '</td></tr>';
                    $tableBody .= $this->getChildData($category->id, 1);
                }
            }

            return response()->json(['data' => $tableBody]);
        }
    }

    /**
     * find child data from parent.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChildData($parentId, $level)
    {
        $categories = Category::where('parent_id', $parentId)->orderBy('title')->get();
        $tbody = '';
        if ($categories) {
            foreach ($categories as $category) {
                $leftpadding = ($level + 1) * 30;
                $data = $category->toArray();
                $tbody .= "<tr><td class='text-center'><input type='radio' id='category_id' name='category_id' value='" . $data['id'] . "' required> </td><td style='padding-left:" . $leftpadding . "px'>" . $data['title'] . '</td></tr>';
                $tbody .= $this->getChildData($category->id, $level + 1);
            }
        }

        return $tbody;
    }
}
