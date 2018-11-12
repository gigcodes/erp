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


class ProductCropperController extends Controller
{
	//
	public function __construct() {

		$this->middleware('permission:imagecropper-list',['only' => ['sList','index']]);
		$this->middleware('permission:imagecropper-create', ['only' => ['create','store']]);
		$this->middleware('permission:imagecropper-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:imagecropper-delete', ['only' => ['destroy']]);
	}

	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=',$stage->get('Supervisor'))
		                   ->whereNull('dnf')
		                   ->withMedia(config('constants.media_tags'))
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'ImageCropper';

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

	public function edit(Sizes $sizes,Product $productimagecropper)
	{

		if( $productimagecropper->isUploaded == 1)
			return redirect(route('products.show',$productimagecropper->id));

		$data = [];

		$data['dnf'] = $productimagecropper->dnf;
		$data['id'] = $productimagecropper->id;
		$data['name'] = $productimagecropper->name;
		$data['short_description'] =$productimagecropper->short_description;
		$data['sku'] = $productimagecropper->sku;
//		$data['supplier_link'] = $productimagecropper->supplier_link;
		$data['description_link'] = $productimagecropper->description_link;
		$data['product_link'] = $productimagecropper->product_link;

		$data['measurement_size_type'] = $productimagecropper->measurement_size_type;
		$data['lmeasurement'] = $productimagecropper->lmeasurement;
		$data['hmeasurement'] = $productimagecropper->hmeasurement;
		$data['dmeasurement'] = $productimagecropper->dmeasurement;

		$data['size_value'] = $productimagecropper->size_value;
		$data['sizes_array'] = $sizes->all();

		$data['size'] = $productimagecropper->size;


		$data['composition'] = $productimagecropper->composition;
		$data['made_in'] = $productimagecropper->made_in;
		$data['brand'] = $productimagecropper->brand;
		$data['color'] = $productimagecropper->color;
		$data['price'] = $productimagecropper->price;

		$data['isApproved'] = $productimagecropper->isApproved;
		$data['rejected_note'] = $productimagecropper->rejected_note;

		$data['images']  = $productimagecropper->getMedia(config('constants.media_tags'));

		$data['category'] = Category::attr(['name' => 'category','class' => 'form-control','disabled' => 'disabled'])
		                            ->selected($productimagecropper->category)
		                            ->renderAsDropdown();

		return view('imagecropper.edit',$data);
	}

	public function update(Request $request,Guard $auth, Product $productimagecropper,Stage $stage)
	{

//		$productattribute->dnf = $request->input('dnf');
		$productimagecropper->stage = $stage->get('ImageCropper');

		/*$productimagecropper->measurement_size_type = $request->input('measurement_size_type');
		$productimagecropper->lmeasurement = $request->input('lmeasurement');
		$productimagecropper->hmeasurement = $request->input('hmeasurement');
		$productimagecropper->dmeasurement = $request->input('dmeasurement');
		$productimagecropper->size = $request->input('size');
		$productimagecropper->color = $request->input('color');

		$productimagecropper->size_value = $request->input('size_value');

		if($request->input('measurement_size_type') == 'size')
			$validations['size_value'] = 'required_without:dnf';
		elseif ( $request->input('measurement_size_type') == 'measurement' ){
			$validations['lmeasurement'] = 'required_without_all:hmeasurement,dmeasurement,dnf|numeric';
			$validations['hmeasurement'] = 'required_without_all:lmeasurement,dmeasurement,dnf|numeric';
			$validations['dmeasurement'] = 'required_without_all:lmeasurement,hmeasurement,dnf|numeric';
		}*/


		$validations  = [];

		//:-( ahead
		$check_image = 0;
		$images = $productimagecropper->getMedia(config('constants.media_tags'));
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

		$this->validate( $request, $validations );

		self::replaceImages($request,$productimagecropper);

		$productimagecropper->last_imagecropper = Auth::id();
		$productimagecropper->save();

		NotificaitonContoller::store( 'has searched', ['Listers'], $productimagecropper->id );
		ActivityConroller::create($productimagecropper->id,'imagecropper','create');

		return redirect()->route( 'productimagecropper.index' )
		                 ->with( 'success', 'ImageCropper updated successfully.' );
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

		return Product::where('last_imagecropper', Auth::id() )
		              ->where('isApproved',-1)
		              ->count();
	}
}
