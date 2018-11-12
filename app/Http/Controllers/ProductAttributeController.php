<?php

namespace App\Http\Controllers;

use App\Category;
use App\Image;
use App\Product;
use App\Setting;
use App\Sizes;
use App\Stage;
use App\Brand;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductAttributeController extends Controller
{
    //
	public function __construct() {

		$this->middleware('permission:attribute-list',['only' => ['sList','index']]);
		$this->middleware('permission:attribute-create', ['only' => ['create','store']]);
		$this->middleware('permission:attribute-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:attribute-delete', ['only' => ['destroy']]);
	}

	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=',$stage->get('Searcher'))
						   ->whereNull('dnf')
		                   ->paginate(Setting::get('pagination'));
		$roletype = 'Attribute';

		$search_suggestions = [];
		$sku_suggestions = ( new Product() )->newQuery()->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		$brand_suggestions = Brand::getAll();

		foreach ($sku_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion['sku']);
		}

		foreach ($brand_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion);
		}

		return view('partials.grid',compact('products','roletype', 'search_suggestions'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public function sList(){

		$productattribute = Product::latest()->withMedia(config('constants.media_tags'))->paginate(Setting::get('pagination'));
		return view('productattribute.list',compact('productattribute'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Sizes $sizes,Product $productattribute)
	{

		if( $productattribute->isApproved == 1)
			return redirect(route('products.show',$productattribute->id));

		$data = [];

		$data['dnf'] = $productattribute->dnf;
		$data['id'] = $productattribute->id;
		$data['name'] = $productattribute->name;
		$data['short_description'] =$productattribute->short_description;

		$data['measurement_size_type'] = $productattribute->measurement_size_type;
		$data['lmeasurement'] = $productattribute->lmeasurement;
		$data['hmeasurement'] = $productattribute->hmeasurement;
		$data['dmeasurement'] = $productattribute->dmeasurement;

		$data['size'] = $productattribute->size;

		$data['size_value'] = $productattribute->size_value;
		$data['sizes_array'] = $sizes->all();

		$data['composition'] = $productattribute->composition;
		$data['sku'] = $productattribute->sku;
		$data['made_in'] = $productattribute->made_in;
		$data['brand'] = $productattribute->brand;
		$data['color'] = $productattribute->color;
		$data['price'] = $productattribute->price;
		$data['price_inr'] = $productattribute->price_inr;
		$data['price_special'] = $productattribute->price_special;
		$data['euro_to_inr'] = $productattribute->euro_to_inr;

		$data['isApproved'] = $productattribute->isApproved;
		$data['rejected_note'] = $productattribute->rejected_note;

		$data['images']  = $productattribute->getMedia(config('constants.media_tags'));

		$data['category'] = Category::attr(['name' => 'category','class' => 'form-control'])
		                                        ->selected($productattribute->category)
		                                        ->renderAsDropdown();
		$data['product_link'] = $productattribute->product_link;
		$data['supplier_link'] = $productattribute->supplier_link;
		$data['description_link'] = $productattribute->description_link;

		return view('productattribute.edit',$data);
	}

	public function update(Request $request,Guard $auth, Product $productattribute,Stage $stage)
	{

		$productattribute->dnf = $request->input('dnf');
		$productattribute->name = $request->input('name');
		$productattribute->short_description = $request->input('short_description');

		$productattribute->measurement_size_type = $request->input('measurement_size_type');
		$productattribute->lmeasurement = $request->input('lmeasurement');
		$productattribute->hmeasurement = $request->input('hmeasurement');
		$productattribute->dmeasurement = $request->input('dmeasurement');

		$productattribute->size = $request->input('size');

		$productattribute->size_value = $request->input('size_value');

		$productattribute->composition = $request->input('composition');
		$productattribute->sku = $request->input('sku');
		$productattribute->made_in = $request->input('made_in');
		$productattribute->brand = $request->input('brand');
		$productattribute->color = $request->input('color');
		$productattribute->price = $request->input('price');

		if(!empty($productattribute->brand)) {
			$productattribute->price_inr     = $this->euroToInr( $productattribute->price, $productattribute->brand );
			$productattribute->price_special = $this->calculateSpecialDiscount( $productattribute->price_inr, $productattribute->brand );
		}

		$productattribute->stage = $stage->get('Attribute');
		$productattribute->category = $request->input('category');
		$productattribute->product_link = $request->input('product_link');
		$productattribute->supplier_link = $request->input('supplier_link');
		$productattribute->description_link = $request->input('description_link');
		$productattribute->last_attributer = Auth::id();

		$validations  = [
			'sku'   => 'required_without:dnf|unique:products,sku,'.$productattribute->id,
			'name'   => 'required_without:dnf',
			'short_description' => 'required_without:dnf',
			'composition' => 'required_without:dnf',
		];

		if($request->input('measurement_size_type') == 'size')
			$validations['size_value'] = 'required_without:dnf';
		elseif ( $request->input('measurement_size_type') == 'measurement' ){
			$validations['lmeasurement'] = 'required_without_all:hmeasurement,dmeasurement,dnf|numeric';
			$validations['hmeasurement'] = 'required_without_all:lmeasurement,dmeasurement,dnf|numeric';
			$validations['dmeasurement'] = 'required_without_all:lmeasurement,hmeasurement,dnf|numeric';
		}

		//:-( ahead
		$check_image = 0;
		$images = $productattribute->getMedia(config('constants.media_tags'));
		$images_no = sizeof($images);

		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage'.$i ) != 0 ) {
				$validations['image.'.$i] = 'mimes:jpeg,bmp,png,jpg';

				if( empty($request->file('image.'.$i ) ) ){
					$check_image++;
				}
			}
		}

		$messages = [];
		if($check_image == $images_no) {
			$validations['image'] = 'required';
			$messages['image.required'] ='Atleast on image is required. Last image can not be removed';
		}
		//:-( over


		$this->validate( $request, $validations, $messages );

		self::replaceImages($request,$productattribute);

		$productattribute->save();

		NotificaitonContoller::store( 'has added attribute', ['Supervisors'], $productattribute->id );
		ActivityConroller::create($productattribute->id,'attribute','create');

		return redirect()->route( 'productattribute.index' )
		                 ->with( 'success', 'Attribute updated successfully.' );
	}

	public function calculateSpecialDiscount($price,$brand) {

//		$dis_per = Setting::get('special_price_discount');
		$dis_per = BrandController::getDeductionPercentage($brand);

		$dis_price = $price - ($price * $dis_per)/100;

		return round($dis_price,-3);
	}

	public function euroToInr($price,$brand){

		$euro_to_inr =  BrandController::getEuroToInr($brand);

		if(!empty($euro_to_inr))
			$inr = $euro_to_inr*$price;
		else
			$inr = Setting::get('euro_to_inr')*$price;

		return round($inr,-3);
	}


	public function replaceImages($request,$productattribute){

		$delete_array = [];
		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage' . $i ) != 0 ) {
				$delete_array[] = $request->input( 'oldImage' . $i );
			}

			if( !empty($request->file('image.'.$i ) ) ){

				$media = MediaUploader::fromSource($request->file('image.'.$i ))->upload();
				$productattribute->attachMedia($media,config('constants.media_tags'));
			}
		}

		$results = Media::whereIn('id' , $delete_array )->get();
		$results->each(function($media) {
			Image::trashImage($media->basename);
			$media->delete();
		});

	}

	public static function rejectedProductCountByUser(){

		return Product::where('last_attributer', Auth::id() )
		        ->where('isApproved',-1)
				->count();
	}

}
