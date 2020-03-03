<?php

namespace App\Http\Controllers;

use App\Category;
use App\ColorReference;
use App\Library\Product\ProductSearch;
use App\Stage;
use Illuminate\Http\Request;

class NewProductInventoryController extends Controller
{
    public function __construct()
    {

    }

    public function index(Stage $stage)
    {
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])->selected(request('category'))->renderAsDropdown();
        $suppliersDropList  = \Illuminate\Support\Facades\DB::select('SELECT id, supplier FROM suppliers INNER JOIN (
                                    SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
                                    ) as product_suppliers
                                ON suppliers.id = product_suppliers.supplier_id');
        $suppliersDropList = collect($suppliersDropList)->pluck("supplier", "id")->toArray();
        $typeList          = [
            "scraped"  => "Scraped",
            "imported" => "Imported",
            "uploaded" => "Uploaded",
        ];

        $params   = request()->all();
        $products = (new ProductSearch($params))->getQuery()->paginate(10);

        $items = [];
        foreach ($products->items() as $product) {
            $date               = date("Y-m-d", strtotime($product->created_at));
            $referencesCategory = "";
            $referencesColor    = "";
            if (isset($product->scraped_products)) {
                // starting to see that howmany category we going to update
                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
                    $category = $product->scraped_products->properties['category'];
                    if (is_array($category)) {
                        $referencesCategory = implode(' > ', $category);
                    }

                }

                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
                    $referencesColor = $product->scraped_products->properties['color'];
                }
            }
            $product->reference_category = $referencesCategory;
            $product->reference_color    = $referencesColor;

            $supplier_list = '';
            foreach ($product->suppliers as $key => $supplier) {
                $supplier_list .= $supplier->supplier;
            }

            $product->supplier_list = $supplier_list;

            if (isset($items[$date])) {
                $items[$date][] = $product;
            } else {
                $items[$date] = [$product];
            }
        }

        // move to the function
        $categoryAll   = Category::where('parent_id', 0)->get();
        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = array('id' => $category->id, 'value' => $category->title);
            $childs          = Category::where('parent_id', $category->id)->get();
            foreach ($childs as $child) {
                $categoryArray[] = array('id' => $child->id, 'value' => $category->title . ' > ' . $child->title);
                $grandChilds     = Category::where('parent_id', $child->id)->get();
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = array('id' => $grandChild->id, 'value' => $category->title . ' > ' . $child->title . ' > ' . $grandChild->title);
                    }
                }
            }
        }

        $categoryArray = collect($categoryArray)->pluck("value", "id")->toArray();
        $sampleColors  = ColorReference::select('erp_color')->groupBy('erp_color')->get()->pluck("erp_color", "erp_color")->toArray();

        return view("product-inventory.index", compact('category_selection', 'suppliersDropList', 'typeList', 'products', 'items', 'categoryArray', 'sampleColors'));
    }
}
