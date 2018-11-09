<?php


namespace App\Http\Controllers;


use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class ProductController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	function __construct()
	{
		$this->middleware('permission:product-list');
		$this->middleware('permission:product-create', ['only' => ['create','store']]);
		$this->middleware('permission:product-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:product-delete', ['only' => ['destroy']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$products = Product::latest()->paginate(10);
		return view('products.index',compact('products'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return view('products.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{

		$this->validate($request,[
			'sku' => 'required',
			'image' => 'required | mimes:jpeg,bmp,png,jpg'
		]);

		$data = [];
		$data['sku'] = $request->input('sku');



		$name = Input::file('image')->getClientOriginalName();
		$extension = Input::file('image')->getClientOriginalExtension();
		$timestamp = date("Y-m-d-His",time());
		$image_name = $name.'-'.$timestamp.'.'.$extension;

		Input::file('image')->move('uploads',$image_name);

		$data['image'] = $image_name;


		$product = new Product();
		$product->sku = $request->input('sku');
		$product->image = $image_name;

		$product->save();
//		Product::create($data);

		return redirect()->route('products.index')
		                 ->with('success','Product created successfully.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Product  $product
	 * @return \Illuminate\Http\Response
	 */
	public function show(Product $product)
	{
		return view('products.show',compact('product'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Product  $product
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Product $product)
	{
		return view('products.edit',compact('product'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Product  $product
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Product $product)
	{
		request()->validate([
			'name' => 'required',
			'detail' => 'required',
		]);


		$product->update($request->all());


		return redirect()->route('products.index')
		                 ->with('success','Product updated successfully');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Product  $product
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Product $product)
	{
		$product->delete();


		return redirect()->route('products.index')
		                 ->with('success','Product deleted successfully');
	}

}