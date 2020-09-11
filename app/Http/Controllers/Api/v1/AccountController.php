<?php

namespace App\Http\Controllers\Api\v1;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function create(Request $request)
    {

        $customer = Customer::where('email', $request->get("email"))->where("store_website_id", $request->get("store_website_id"))->first();

        // Create a customer if doesn't exists
        if (!$customer) {
            $customer = new Customer;
        }

        $customer->name             = trim($request->get("firstname") . " " . $request->get("lastname"));
        $customer->email            = $request->get("email");
        $customer->store_website_id = $request->get("store_website_id");
        $customer->save();

        return response()->json(["code" => 200, "message" => "Customer has been account created", "data" => $customer]);
    }
}
