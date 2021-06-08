<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Customer;
use App\Supplier;
use App\User;
use App\Vendor;
use Illuminate\Http\Request;

class Select2Controller extends Controller
{

    public function customers(Request $request){

        $customers = Customer::select('id', 'name', 'email');

        if (!empty($request->q)) {

            $customers->where(function($q) use($request){
                $q->where('name', 'LIKE', '%'. $request->q .'%')
                ->orWhere('email', 'LIKE', '%'. $request->q .'%');
            });

        }

        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {

            $result['items'][] = [
                'id' => $customer->id,
                'text' => $customer->name
            ];
        }

        return response()->json($result);

    }
    public function suppliers(Request $request){

        $suppliers = Supplier::select('id', 'supplier')->paginate(30);

        // if (!empty($request->q)) {

        //     // $suppliers->where(function($q) use($request){
        //     //     $q->where('supplier', 'LIKE', '%'. $request->q .'%')
        //     //     ->orWhere('email', 'LIKE', '%'. $request->q .'%');
        //     // });

        // }
        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {

            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->supplier
            ];
        }
        return response()->json($result);

    }


    public function scrapedBrand(Request $request){

        $scrapedBrandsRaw = Supplier::whereNotNull('scraped_brands_raw')->paginate(30);
        // return $scrapedBrandsRaw;
        // dd($scrapedBrandsRaw);
        $rawBrands        = array();
        foreach ($scrapedBrandsRaw as $key => $value) {
            array_push($rawBrands, array_unique(array_filter(array_column(json_decode($value->scraped_brands_raw, true), 'name'))));
            array_push($rawBrands, array_unique(array_filter(explode(",", $value->scraped_brands))));
        }
        $scrapedBrands = array_unique(array_reduce($rawBrands, 'array_merge', []));
        // dd($scrapedBrands);
// return $scrapedBrands;  
        // $result['total_count'] = count($scrapedBrands);
        // $result['total_count'] = $scrapedBrands->total();
        // $result['incomplete_results'] = $scrapedBrands->nextPageUrl() !== null;
        $result['total_count'] = count($scrapedBrands);
        $result['incomplete_results'] = $scrapedBrandsRaw->nextPageUrl() !== null;


        foreach ($scrapedBrands as $key=> $supplier) {

            $result['items'][] = [
                'id' => $supplier,
                'text' => $supplier
            ];
        }

        return response()->json($result);

    }
    public function updatedbyUsers(Request $request){

        $suppliers = User::select('id', 'name');

      
        $suppliers = $suppliers->paginate(30);

        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {

            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->name
            ];
        }

        return response()->json($result);

    }
    
    public function users(Request $request){

        $users = User::select('id', 'name', 'email');

        if (!empty($request->q)) {

            $users->where(function($q) use($request){
                $q->where('name', 'LIKE', '%'. $request->q .'%')
                ->orWhere('email', 'LIKE', '%'. $request->q .'%');
            });

        }

        $users = $users->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {

            $text = $user->name;

            if($request->format === 'name-email'){
                $text = $user->name .' - ' . $user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text
            ];
        }

        return response()->json($result);

    }

    public function users_vendors(Request $request){
        $users = User::select('id', 'name', 'email');

        if (!empty($request->q)) {

            $users->where(function($q) use($request){
                $q->where('name', 'LIKE', '%'. $request->q .'%')
                ->orWhere('email', 'LIKE', '%'. $request->q .'%');
            });

        }

        $users = $users->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {

            $text = $user->name;

            if($request->format === 'name-email'){
                $text = $user->name .' - ' . $user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text
            ];
        }

        $vendors = Vendor::select('id', 'name', 'email');
        if (!empty($request->q)) {

            $vendors->where(function($q) use($request){
                $q->where('name', 'LIKE', '%'. $request->q .'%')
                ->orWhere('email', 'LIKE', '%'. $request->q .'%');
            });

        }
        $vendors = $vendors->paginate(30);

        $result_vendors['vendors_total_count'] = $vendors->total();
        $result_vendors['vendors_incomplete_results'] = $vendors->nextPageUrl() !== null;

        foreach ($vendors as $user) {

            $text = $user->name;

            if($request->format === 'name-email'){
                $text = $user->name .' - ' . $user->email;
            }

            $result_vendors['items'][] = [
                'id' => $user->id,
                'text' => $text
            ];
        }

        array_push($result,$result_vendors);

        return response()->json($result);
    }
   
}
