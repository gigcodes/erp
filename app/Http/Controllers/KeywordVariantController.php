<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\HashTag;
use App\Jobs\CreateHashTags;
use Illuminate\Http\Request;
use App\KeywordSearchVariants;
use Yajra\DataTables\DataTables;

class KeywordVariantController extends Controller
{

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $variant = new KeywordSearchVariants();
            $variant->keyword = $request->keyword;
            $variant->save();

            /* Initialize queue for add - */
            $brandList = Brand::getAll();
            $categories = Category::getCategoryHierarchyString(4);
            if (!empty($brandList)) {
                ini_set('max_execution_time', '-1');
                ini_set('max_execution_time', '0'); // for infinite time of execution

                $string_arr = [];
                foreach ($brandList as $brand) {
                    foreach($categories as $category) {
                        $string_data['hashtag'] = $brand. ' ' . $category->combined_string .' '. $variant->keyword;
                        $string_data['platforms_id'] = 2;
                        $string_data['rating'] = 8;
                        $string_data['created_at'] = $string_data['updated_at'] = date('Y-m-d h:i:s');
                        $string_data['created_by'] = \Auth::user()->id;
                        $check_exist = HashTag::where('hashtag', $string_data['hashtag'])->count();
                        if($check_exist <= 0) {
                            $string_arr[] = $string_data;
                        }
                    }

                    $chunks = array_chunk($string_arr, 1000);
                    foreach ($chunks as $chunk) {
                        CreateHashTags::dispatch($chunk)->onQueue('generategooglescraperkeywords');
                    }
                    $string_arr = [];
                    /*$processed_brand_id_array[] = $brand->id;
                    CreateHashTags::dispatch($string_arr)->onQueue('insert-hash-tags');
                    $string_arr = [];*/
                }
                KeywordSearchVariants::updateStatusIsHashtagsGeneratedKeywordVariants();
            }

            return response()->json(['message' => "Variant add successfully"]);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KeywordSearchVariants::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function delete(Request $request, $id)
    {

        $variant = KeywordSearchVariants::find($id);

        if ($variant) {
            $variant->delete();
        }

        return response()->json(['message' => 'delete successfully']);
    }
}
