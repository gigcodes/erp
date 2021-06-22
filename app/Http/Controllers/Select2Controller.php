<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;
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



    public function customersByMultiple(Request $request){

        $term = request()->get("q", null);
        $customers = \App\Customer::select('id', 'name', 'phone')->where("name", "like", "%{$term}%")->orWhere("phone", "like", "%{$term}%")->orWhere("id", "like", "%{$term}%");
 
        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {

            $result['items'][] = [
                'id' => $customer->id,
                'text' => '<strong>Name</strong>: ' . $customer->name . ' <strong>Phone</strong>: ' . $customer->phone
            ];
        }

        return response()->json($result);

    }
   
}
