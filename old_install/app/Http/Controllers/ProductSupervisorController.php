<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductSupervisorController extends Controller
{
    public function __construct() {

	    $this->middleware('permission:supervisor-list',['only' => ['index']]);
	    $this->middleware('permission:supervisor-edit',['only' => ['edit','approve']]);
    }


	public function index(){

		$products = Product::latest()->where('stage','2')->paginate(10);
		$roletype = 'Supervisor';

		return view('partials.grid',compact('products','roletype'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(){
		return '';
	}

	public function approve(Product $product){

		$product->isApproved = !$product->isApproved;
		$product->save();

		return ['msg'=>'success', 'isApproved'  => $product->isApproved ];
	}
}
