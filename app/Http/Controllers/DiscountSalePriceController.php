<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\StoreWebsiteSalesPrice;

class DiscountSalePriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $discountsaleprice = StoreWebsiteSalesPrice::select('store_website_sales_prices.*', 'suppliers.supplier')
        ->leftJoin('suppliers', 'store_website_sales_prices.supplier_id', 'suppliers.id');

        if ($request->type != '') {
            $discountsaleprice->where('type', $request->type);
        }
        if ($request->type_id != '') {
            $discountsaleprice->where('type_id', $request->type_id);
        }
        if ($request->supplier != '') {
            $discountsaleprice->where('supplier_id', $request->supplier);
        }
        $discountsaleprice = $discountsaleprice->get();
        $supplier = \App\Supplier::get();
        if ($request->ajax()) {
            return view('discountsaleprice.index_page', [
                'discountsaleprice' => $discountsaleprice,
                'supplier' => $supplier,
            ]);
        } else {
            return view('discountsaleprice.index', [
                'discountsaleprice' => $discountsaleprice,
                'supplier' => $supplier,
            ]);
        }
    }

    public function type(Request $request)
    {
        $type = $request->type;
        $select = "<select class='form-control' name='type_id' required id='type_id'>";

        if ($type == 'brand') {
            $model_type = \App\Brand::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $select .= "<option value='" . $r->id . "'>" . $r->name . '</option>';
            }
        }
        if ($type == 'category') {
            $model_type = \App\Category::class;
            $rs = $model_type::all();
            $data = '';
            foreach ($rs as $r) {
                $select .= "<option value='" . $r->id . "'>" . $r->title . '</option>';
            }
        }

        if ($type == 'product') {
            $model_type = \App\Product::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $select .= "<option value='" . $r->id . "'>" . $r->name . '</option>';
            }
        }

        if ($type == 'store_website') {
            $model_type = \App\StoreWebsite::class;
            $rs = $model_type::get();
            $data = '';
            foreach ($rs as $r) {
                $select .= "<option value='" . $r->id . "'>" . $r->title . '</option>';
            }
        }
        $select .= '</select>';
        echo $select;
    }

    public function create(Request $request)
    {
        $data = $request->except(['_token', 'file']);
        $data['created_by'] = Auth::id();

        $id = $request->id;
        if ($id > 0) {
            StoreWebsiteSalesPrice::where('id', $id)->update($data);

            return redirect('discount-sale-price')->withSuccess('You have successfully updated a record!');
        } else {
            StoreWebsiteSalesPrice::insert($data);

            return redirect('discount-sale-price')->withSuccess('You have successfully added a record!');
        }
    }

    public function delete($id)
    {
        StoreWebsiteSalesPrice::where('id', $id)->delete();

        return redirect('discount-sale-price')->withSuccess('You have successfully deleted a record!');
    }
}
