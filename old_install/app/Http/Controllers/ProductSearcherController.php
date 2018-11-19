<?php

namespace App\Http\Controllers;

use App\Image;
use App\Product;
use App\Setting;
use App\Sizes;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductSearcherController extends Controller
{
    //
	public function __construct() {

		$this->middleware('permission:searcher-list',['only' => ['sList','index']]);
		$this->middleware('permission:searcher-create', ['only' => ['create','store']]);
		$this->middleware('permission:searcher-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:searcher-delete', ['only' => ['destroy']]);
	}

	public function index(){

		$products = Product::latest()->withMedia(config('constants.media_tags'))->paginate(10);
		$roletype = 'Searcher';

		return view('partials.grid',compact('products','roletype'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public function sList(){

		$productsearcher = Product::latest()->withMedia(config('constants.media_tags'))->paginate(10);
		return view('productsearcher.list',compact('productsearcher'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Sizes $sizes,Product $productsearcher)
	{
		$data = [];

		$data['dnf'] = $productsearcher->dnf;
		$data['id'] = $productsearcher->id;
		$data['name'] = $productsearcher->name;
		$data['short_description'] =$productsearcher->short_description;

		$data['measurement_size_type'] = $productsearcher->measurement_size_type;
		$data['lmeasurement'] = $productsearcher->lmeasurement;
		$data['hmeasurement'] = $productsearcher->hmeasurement;
		$data['dmeasurement'] = $productsearcher->dmeasurement;

		$data['size_value'] = $productsearcher->size_value;
		$data['sizes_array'] = $sizes->all();

		$data['composition'] = $productsearcher->composition;
		$data['sku'] = $productsearcher->sku;
		$data['made_in'] = $productsearcher->made_in;
		$data['brand'] = $productsearcher->brand;
		$data['color'] = $productsearcher->color;
		$data['price'] = $productsearcher->price;

		$data['images']  = $productsearcher->getMedia(config('constants.media_tags'));

		return view('productsearcher.edit',$data);
	}

	public function update(Request $request,Guard $auth, Product $productsearcher)
	{

		$productsearcher->dnf = $request->input('dnf');
		$productsearcher->name = $request->input('name');
		$productsearcher->short_description = $request->input('short_description');

		$productsearcher->measurement_size_type = $request->input('measurement_size_type');
		$productsearcher->lmeasurement = $request->input('lmeasurement');
		$productsearcher->hmeasurement = $request->input('hmeasurement');
		$productsearcher->dmeasurement = $request->input('dmeasurement');

		$productsearcher->size_value = $request->input('size_value');

		$productsearcher->composition = $request->input('composition');
		$productsearcher->sku = $request->input('sku');
		$productsearcher->made_in = $request->input('made_in');
		$productsearcher->brand = $request->input('brand');
		$productsearcher->color = $request->input('color');
		$productsearcher->price = $request->input('price');
		$productsearcher->price_inr  = $this->euroToInr($productsearcher->price );
		$productsearcher->price_special = $this->calculateSpecialDiscount($productsearcher->price_inr);
		$productsearcher->stage = $request->input('stage');


		$validations  = [
			'sku'   => 'required_without:dnf|unique:products,sku,'.$productsearcher->id,
			'name'   => 'required_without:dnf',
			'short_description' => 'required_without:dnf',
			'composition' => 'required_without:dnf',
		];

		if($request->input('measurement_size_type') == 'size')
			$validations['size_value'] = 'required_without:dnf';
		elseif ( $request->input('measurement_size_type') == 'measurement' ){
			$validations['lmeasurement'] = 'required_without_all:hmeasurement,dmeasurement,dnf';
			$validations['hmeasurement'] = 'required_without_all:lmeasurement,dmeasurement,dnf';
			$validations['dmeasurement'] = 'required_without_all:lmeasurement,hmeasurement,dnf';
		}

		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage'.$i ) != 0 ) {
				$validations['image.'.$i] = 'mimes:jpeg,bmp,png,jpg';
			}
		}

		$this->validate( $request, $validations );

		$delete_array = [];
		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage' . $i ) != 0 ) {
				$delete_array[] = $request->input( 'oldImage' . $i );
			}
			else if( !empty($request->file('image.'.$i ) ) ){

				$media = MediaUploader::fromSource($request->file('image.'.$i ))->upload();
				$productsearcher->attachMedia($media,config('constants.media_tags'));
			}
		}

		$results = Media::whereIn('id' , $delete_array )->get();
		$results->each(function($media) {
			$media->delete();
		});


		$productsearcher->save();

		NotificaitonContoller::store( 'has searched', ['Supervisor','Admin'], $productsearcher->id );

		return redirect()->route( 'productsearcher.index' )
		                 ->with( 'success', 'Searcher updated successfully.' );
	}

	public function calculateSpecialDiscount($price) {

		$dis_per = Setting::get('special_price_discount');

		$dis_price = $price - ($price * $dis_per)/100;

		return round($dis_price,-3);
	}

	public function euroToInr($price){

		$inr = Setting::get('euro_to_inr')*$price;
		return round($inr);
	}

}
