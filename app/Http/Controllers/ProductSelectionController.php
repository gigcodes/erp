<?php

namespace App\Http\Controllers;


use App\Image;
use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use App\Supplier;
use App\ReadOnly\LocationList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductSelectionController extends Controller
{


	public function __construct() {

		//$this->middleware('permission:selection-list',['only' => ['sList','index']]);
		//$this->middleware('permission:selection-create', ['only' => ['create','store']]);
		//$this->middleware('permission:selection-edit', ['only' => ['edit','update']]);

		//$this->middleware('permission:selection-delete', ['only' => ['destroy']]);
	}

	public function index(){
		$products = Product::where('stock', '>=', 1)->latest()
											->withMedia(config('constants.media_tags'))
											->with('suppliers')
											->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
											->paginate(Setting::get('pagination'));

		$roletype = 'Selection';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function sList(){

		$productselection = Product::latest()->withMedia(config('constants.media_tags'))->paginate(Setting::get('pagination'));

		return view('productselection.list',compact('productselection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function create()
	{
		$locations = (new LocationList)->all();
		$suppliers = Supplier::select(['id', 'supplier'])->get();

		return view('productselection.create', [
			'locations'	=> $locations,
			'suppliers'	=> $suppliers
		]);
	}


	public function show(Product $productselection)
	{
//		$productselection->image
		return view('productselection.show',compact('productselection'));
	}

	public function store(Request $request, Stage $stage){

		$this->validate($request,[
			'sku' => 'required|unique:products',
			'image' => 'required | mimes:jpeg,bmp,png,jpg',
		]);

		$productselection = new Product();
		$productselection->sku = $request->input('sku');
		$productselection->size = $request->input('size');
		$productselection->price = $request->input('price');
		// $productselection->supplier = $request->input('supplier');
		$productselection->supplier_link = $request->input('supplier_link');
		$productselection->location = $request->input('location');
		$productselection->brand = $request->input('brand');
//		$productselection->description_link = $request->input('description_link');
//		$productselection->image = Image::newImage();
		$productselection->last_selector = Auth::id();

		$productselection->stage = $stage->get('Selection');
		$productselection->stock = 1;

		if(!empty($productselection->brand) && !empty($productselection->price)) {
			$productselection->price_inr     = $this->euroToInr($productselection->price, $productselection->brand);
			$productselection->price_special = $this->calculateSpecialDiscount($productselection->price_inr, $productselection->brand);
		} else {
			$productselection->price_special = $request->price_special;
		}


		$productselection->save();

		if ($request->supplier) {
			$productselection->suppliers()->attach($request->supplier);
		}

		$productselection->detachMediaTags(config('constants.media_tags'));
		$media = MediaUploader::fromSource($request->file('image'))
								->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
								->upload();
		$productselection->attachMedia($media,config('constants.media_tags'));

		NotificaitonContoller::store('has selected',['Searchers'], $productselection->id);

		ActivityConroller::create($productselection->id,'selection','create');

		return redirect()->route('productselection.index')
		                 ->with('success','Selection created successfully.');
	}

	public function edit(Product $productselection)
	{
		if( $productselection->isApproved == 1)
			return redirect(route('products.show',$productselection->id));

		$locations = (new LocationList)->all();
		$suppliers = Supplier::select(['id', 'supplier'])->get();

		return view('productselection.edit',compact('productselection', 'locations', 'suppliers'));
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
		// $productselection->supplier = $request->input('supplier');
		$productselection->supplier_link = $request->input('supplier_link');
		$productselection->location = $request->input('location');
		$productselection->brand = $request->input('brand');
//		$productselection->description_link = $request->input('description_link');
		$productselection->last_selector = Auth::id();

		if(!empty($productselection->brand) && !empty($productselection->price)) {
			$productselection->price_inr     = $this->euroToInr($productselection->price, $productselection->brand);
			$productselection->price_special = $this->calculateSpecialDiscount($productselection->price_inr, $productselection->brand);
		} else {
			$productselection->price_special = $request->price_special;
		}

		if ($request->oldImage > 0) {
			self::replaceImage($request,$productselection);
		} elseif ($request->oldImage == -1) {
			$media = MediaUploader::fromSource( $request->file( 'image' ) )
									->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
									->upload();
			$productselection->attachMedia( $media, config( 'constants.media_tags' ) );
		}

//		$product->update($request->all());

		$productselection->save();

		if ($request->supplier) {
			$productselection->suppliers()->detach();
			$productselection->suppliers()->attach($request->supplier);
		}

		NotificaitonContoller::store('has updated',['Searchers'], $productselection->id);

		return redirect()->route('productselection.index')
		                 ->with('success','Selection updated successfully');
	}

	public function replaceImage($request,$productselection){


		if( $request->input('oldImage') != 0) {

			$results = Media::where('id' , $request->input('oldImage') )->get();

			$results->each(function($media) {
				Image::trashImage($media->basename);
				$media->delete();
			});

			if( !empty($request->file('image') ) ) {

				$media = MediaUploader::fromSource( $request->file( 'image' ) )
										->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
										->upload();
				$productselection->attachMedia( $media, config( 'constants.media_tags' ) );
			}
		}

	}

	public function euroToInr($price,$brand){

		$euro_to_inr =  BrandController::getEuroToInr($brand);

		if(!empty($euro_to_inr))
			$inr = $euro_to_inr*$price;
		else
			$inr = Setting::get('euro_to_inr')*$price;

		return round($inr,-3);
	}

	public function calculateSpecialDiscount($price,$brand) {

//		$dis_per = Setting::get('special_price_discount');
		$dis_per = BrandController::getDeductionPercentage($brand);

		$dis_price = $price - ($price * $dis_per)/100;

		return round($dis_price,-3);
	}
}
