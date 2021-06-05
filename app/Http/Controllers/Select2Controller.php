<?php

namespace App\Http\Controllers;

use App\Customer;
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
