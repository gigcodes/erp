<?php

namespace App\Http\Controllers;

use App\Category;
use App\Image;
use App\Product;
use App\Setting;
use App\Sizes;
use App\Stage;
use App\Brand;
use File;
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
												->where('stock', '>=', 1)
		                   ->where('stage','>=',$stage->get('Supervisor'))
		                   ->whereNull('dnf')
		                   ->withMedia(config('constants.media_tags'))
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'ImageCropper';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
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
		$data['location'] = $productimagecropper->location;
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
		$data['isUploaded'] = $productimagecropper->isUploaded;
		$data['isFinal'] = $productimagecropper->isFinal;
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

	public function getListOfImagesToBeVerified(Stage $stage) {
	    $products = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('is_crop_rejected', 0)
            ->paginate(24);

	    return view('products.crop_list', compact('products'));
    }

    public function showImageToBeVerified($id, Stage $stage) {
	    $product = Product::find($id);
        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->first();

	    return view('products.crop', compact('product', 'secondProduct'));
    }

    public function approveCrop($id,Stage $stage) {
	    $product = Product::findOrFail($id);
	    ++$product->stage;
	    $product->save();

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->first();

        $this->deleteUncroppedImages($product);

        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id)->with('message', 'Cropping approved successfully!');
    }

    private function deleteUncroppedImages($product) {
        if ($product->hasMedia(config('constants.media_tags'))) {
            $tc = count($product->getMedia(config('constants.media_tags')));
            foreach ($product->getMedia(config('constants.media_tags')) as $key=>$image) {
                if ($key+1 <= $tc/2) {
                    $image_path = $image->getAbsolutePath();

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $image->delete();
                }
            }

            $product->is_image_processed = 0;
            $product->save();

        }
    }

    public function rejectCrop($id,Stage $stage, Request $request) {
        $product = Product::findOrFail($id);
        $product->is_crop_rejected = 1;
        $product->crop_remark = $request->get('remark');
        $product->save();

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->first();

        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id)->with('message', 'Cropping rejected!');
    }

    public function showRejectedCrops()
    {
        $products = Product::where('is_crop_rejected', 1)->paginate(24);

        return view('products.rejected_crop_list', compact('products'));
    }

    public function showRejectedImageToBeverified($id) {
	    $product = Product::find($id);
	    $secondProduct = Product::where('id', '!=', $id)->where('is_crop_rejected', 1)->first();

	    return view('products.rejected_crop', compact('product', 'secondProduct'));
    }

    public function approveRejectedCropped($id, Request $request) {

    }
}
