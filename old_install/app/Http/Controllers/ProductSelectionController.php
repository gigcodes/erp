<?php

namespace App\Http\Controllers;


use App\Image;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductSelectionController extends Controller
{


	public function __construct() {

		$this->middleware('permission:selection-list',['only' => ['sList','index']]);
		$this->middleware('permission:selection-create', ['only' => ['create','store']]);
		$this->middleware('permission:selection-edit', ['only' => ['edit','update']]);

		$this->middleware('permission:selection-delete', ['only' => ['destroy']]);
	}

	public function index(){

		$products = Product::latest()->withMedia(config('constants.media_tags'))->paginate(10);

		$roletype = 'Selection';

		return view('partials.grid',compact('products','roletype'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function sList(){

		$productselection = Product::latest()->withMedia(config('constants.media_tags'))->paginate(10);

		return view('productselection.list',compact('productselection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function create()
	{
		return view('productselection.create');
	}


	public function show(Product $productselection)
	{
//		$productselection->image
		return view('productselection.show',compact('productselection'));
	}

	public function store(Request $request){

		$this->validate($request,[
			'sku' => 'required|unique:products',
			'image' => 'required | mimes:jpeg,bmp,png,jpg',
		]);

		$productselection = new Product();
		$productselection->sku = $request->input('sku');
		$productselection->size = $request->input('size');
		$productselection->price = $request->input('price');
//		$productselection->image = Image::newImage();

		$productselection->stage = $request->input('stage');

		$productselection->save();

		$media = MediaUploader::fromSource($request->file('image'))->upload();
		$productselection->attachMedia($media,config('constants.media_tags'));

		NotificaitonContoller::store('has selected',['Searchers','Admin'], $productselection->id);

		return redirect()->route('productselection.index')
		                 ->with('success','Selection created successfully.');
	}

	public function edit(Product $productselection)
	{
		return view('productselection.edit',compact('productselection'));
	}

	public function update(Request $request, Product $productselection)
	{
		$validations  = [
			'sku'   => 'required|unique:products,sku,'.$productselection->id,
		];

		if( $request->input('oldImage') != 0)
			$validations['image'] = 'required | mimes:jpeg,bmp,png,jpg';

		$this->validate( $request,  $validations);


		$productselection->sku = $request->input('sku');
		$productselection->size = $request->input('size');
		$productselection->price = $request->input('price');
//		$productselection->stage = $request->input('stage');

		if( $request->input('oldImage') != 0) {

			$media = Media::where('id' , $request->input('oldImage') )->get();
			$media->delete();

			$media = MediaUploader::fromSource($request->file('image'))->upload();
			$productselection->attachMedia($media,config('constants.media_tags'));
		}

//		$productselection->image = Image::replaceImage( $productselection->image );
//		$product->update($request->all());

		$productselection->save();

		NotificaitonContoller::store('has updated',['Searchers','Admin'], $productselection->id);

		return redirect()->route('productselection.index')
		                 ->with('success','Selection updated successfully');
	}
}
