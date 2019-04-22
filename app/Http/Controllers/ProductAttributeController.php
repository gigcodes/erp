<?php

namespace App\Http\Controllers;

use App\Category;
use App\Image;
use App\Product;
use App\ScrapedProducts;
use App\Setting;
use App\Supplier;
use App\Sizes;
use App\Stage;
use App\Brand;
use App\ReadOnly\LocationList;
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
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));
		$roletype = 'Attribute';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public function sList(){

		$productattribute = Product::latest()->withMedia(config('constants.media_tags'))->paginate(Setting::get('pagination'));
		return view('productattribute.list',compact('productattribute'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Sizes $sizes,Product $productattribute)
	{

		// if( $productattribute->isApproved == 1)
		// 	return redirect(route('products.show',$productattribute->id));

		$data = [];

		$data['dnf'] = $productattribute->dnf;
		$data['id'] = $productattribute->id;
		$data['name'] = $productattribute->name;
		$data['short_description'] =$productattribute->short_description;

		$data['measurement_size_type'] = $productattribute->measurement_size_type;
		$data['lmeasurement'] = $productattribute->lmeasurement;
		$data['hmeasurement'] = $productattribute->hmeasurement;
		$data['dmeasurement'] = $productattribute->dmeasurement;

		$data['size'] = $productattribute->size ? explode(',', $productattribute->size) : [];

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
		$data['suppliers'] = Supplier::all();
		$data['product_suppliers'] = $productattribute->suppliers;

		$data['isApproved'] = $productattribute->isApproved;
		$data['rejected_note'] = $productattribute->rejected_note;

		$data['images']  = $productattribute->getMedia(config('constants.media_tags'));

		$data['category'] = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'product-category'])
		                                        ->selected($productattribute->category)
		                                        ->renderAsDropdown();

    $data['old_category'] = $productattribute->category;
		$data['category_tree'] = [];
		$data['categories_array'] = [];

		foreach (Category::all() as $category) {
			if ($category->parent_id != 0) {
				$parent = $category->parent;
				if ($parent->parent_id != 0) {
					$data['category_tree'][$parent->parent_id][$parent->id][$category->id];
				} else {
					$data['category_tree'][$parent->id][$category->id] = $category->id;
				}
			}

			$data['categories_array'][$category->id] = $category->parent_id;
		}

		$data['product_link'] = $productattribute->product_link;
		$data['supplier'] = $productattribute->supplier;
		$data['supplier_link'] = $productattribute->supplier_link;
		$data['description_link'] = $productattribute->description_link;
		$data['location'] = $productattribute->location;
		$data['reference'] = ScrapedProducts::where('sku', $productattribute->sku)->first() ? ScrapedProducts::where('sku', $productattribute->sku)->first()->properties : [];
		$data['locations'] = (new LocationList)->all();

		return view('productattribute.edit',$data);
	}

	public function update(Request $request,Guard $auth, Product $productattribute,Stage $stage)
	{
		$old_sizes = $productattribute->size;
		$old_color = $productattribute->color;
		$old_images = $productattribute->getMedia(config('constants.media_tags'));

		$productattribute->dnf = $request->input('dnf');
		$productattribute->name = $request->input('name');
		$productattribute->short_description = $request->input('short_description');

		$productattribute->measurement_size_type = $request->input('measurement_size_type');
		$productattribute->lmeasurement = $request->input('lmeasurement');
		$productattribute->hmeasurement = $request->input('hmeasurement');
		$productattribute->dmeasurement = $request->input('dmeasurement');

		$productattribute->size = $request->size ? implode(',', $request->size) : ($request->other_size ?? "");

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

		if ($productattribute->stage < $stage->get('Attribute')) {
			$productattribute->stage = $stage->get('Attribute');
		} else if ($productattribute->stage == $stage->get('Attribute')) {
			$productattribute->stage = 4;
		}

		$productattribute->category = $request->input('category');
		$productattribute->product_link = $request->input('product_link');
		// $productattribute->supplier = $request->input('supplier');
		$productattribute->supplier_link = $request->input('supplier_link');
		$productattribute->description_link = $request->input('description_link');
		$productattribute->location = $request->input('location');
		$productattribute->last_attributer = Auth::id();

		$validations  = [
			'sku'   => 'required_without:dnf|unique:products,sku,'.$productattribute->id,
			// 'name'   => 'required_without:dnf',
			// 'short_description' => 'required_without:dnf',
			// 'composition' => 'required_without:dnf',
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

		if ($request->supplier) {
			$productattribute->suppliers()->detach();
			$productattribute->suppliers()->attach($request->supplier);
		}

		$success_message = 'Attribute updated successfully. ';

		if ($productattribute->isUploaded == 1) {
			$result = $this->magentoProductUpdate($productattribute, $old_sizes, $old_color, $old_images);

			if (!$result[1]) {
				$success_message .= "Not everything was updated correctly. Check product on Magento";
			}
		}

		NotificaitonContoller::store('has added attribute', ['Supervisors'], $productattribute->id);
		ActivityConroller::create($productattribute->id,'attribute','create');

		return redirect()->route( 'productimagecropper.index' )
		                 ->with( 'success', $success_message);
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

	public function magentoProductUpdate($product, $old_sizes, $old_color, $old_images) {
		$options = array(
			'trace' => true,
			'connection_timeout' => 120,
			'wsdl_cache' => WSDL_CACHE_NONE,
		);

		$proxy = new \SoapClient(config('magentoapi.url'), $options);
		$sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

		$sku = $product->sku . $product->color;
		$old_sku = $product->sku . $old_color;
		$categories = CategoryController::getCategoryTreeMagentoIds($product->category);
		$brand= $product->brands()->get();

		array_push($categories,$brand[0]->magento_id);

		// DELETES OLD SIMPLE PRODUCTS
		$deleted_count = 0;
		if(!empty($old_sizes)) {
			$sizes_array = explode(',', $old_sizes);

			foreach ($sizes_array as $size) {
				try {
					$result = $proxy->catalogProductDelete($sessionId, $old_sku . "-" . $size);

					$deleted_count++;
				} catch (\Exception $e) {
					$error_message = $e->getMessage();
				}
			}
		}

		if(!empty($product->size)) {
			$associated_skus = [];
			$sizes_array = explode(',', $product->size);

			foreach ($sizes_array as $size) {
				$productData = array(
					'categories'            => $categories,
					'name'                  => $product->name,
					'description'           => '<p></p>',
					'short_description'     => $product->short_description,
					'website_ids'           => array(1),
					// Id or code of website
					'status'                => $product->isFinal ?? 2,
					// 1 = Enabled, 2 = Disabled
					'visibility'            => 1,
					// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
					'tax_class_id'          => 2,
					// Default VAT
					'weight'                => 0,
					'stock_data' => array(
						'use_config_manage_stock' => 1,
						'manage_stock' => 1,
						'qty'					=> $product->stock,
						'is_in_stock'	=> $product->stock > 1 ? 1 : 0,
					),
					'price'                 => $product->price_inr,
					// Same price than configurable product, no price change
					'special_price'         => $product->price_special,
					'additional_attributes' => array(
						'single_data' => array(
							array( 'key' => 'composition', 'value' => $product->composition, ),
							array( 'key' => 'color', 'value' => $product->color, ),
							array( 'key' => 'sizes', 'value' => $size, ),
							array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
							array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
						),
					),
				);

				// Creation of product simple
				$result            = $proxy->catalogProductCreate($sessionId, 'simple', 14, $sku . '-' . $size, $productData);
				$associated_skus[] = $sku . '-' . $size;
			}

			/**
			 * Configurable product
			 */
			$productData = array(
				'categories'              => $categories,
				'name'                    => $product->name,
				'description'             => '<p></p>',
				'short_description'       => $product->short_description,
				'website_ids'             => array(1),
				// Id or code of website
				// 'status'                  => 2,
				// 1 = Enabled, 2 = Disabled
				// 'visibility'              => 4,
				// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
				// 'tax_class_id'            => 2,
				// Default VAT
				// 'weight'                  => 0,
				'stock_data' => array(
					'use_config_manage_stock' => 1,
					'manage_stock' => 1,
					'qty'					=> $product->stock,
					'is_in_stock'	=> $product->stock > 1 ? 1 : 0,
				),
				'price'                   => $product->price_inr,
				// Same price than configurable product, no price change
				'special_price'           => $product->price_special,
				'associated_skus'         => $associated_skus,
				// Simple products to associate
				// 'configurable_attributes' => array( 155 ),
				'additional_attributes'   => array(
					'single_data' => array(
						array( 'key' => 'composition', 'value' => $product->composition, ),
						array( 'key' => 'color', 'value' => $product->color, ),
						array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
						array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
					),
				),
			);

			// Creation of configurable product
			$error_message = '';
			$updated_product = 0;
			try {
				$result = $proxy->catalogProductUpdate($sessionId, $sku, $productData);
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$productData['status'] = $product->isFinal ?? 2;
				$productData['visibility'] = 4;
				$productData['tax_class_id'] = 2;
				$productData['weight'] = 0;

				try {
					$result = $proxy->catalogProductDelete($sessionId, $old_sku);

					$deleted_count++;
				} catch (\Exception $e) {
					$error_message = $e->getMessage();
				}

				$result = $proxy->catalogProductCreate($sessionId, 'configurable', 14, $sku, $productData);
			} else {
				$updated_product = 1;
			}
		} else {
			$measurement = 'L-'.$product->lmeasurement.',H-'.$product->hmeasurement.',D-'.$product->dmeasurement;

			$productData = array(
				'categories'            => $categories,
				'name'                  => $product->name,
				'description'           => '<p></p>',
				'short_description'     => $product->short_description,
				'website_ids'           => array(1),
				// Id or code of website
				// 'status'                => 2,
				// 1 = Enabled, 2 = Disabled
				// 'visibility'            => 4,
				// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
				// 'tax_class_id'          => 2,
				// Default VAT
				// 'weight'                => 0,
				'stock_data' => array(
					'use_config_manage_stock' => 1,
					'manage_stock' => 1,
					'qty'					=> $product->stock,
					'is_in_stock'	=> $product->stock > 1 ? 1 : 0,
				),
				'price'                 => $product->price_inr,
				// Same price than configurable product, no price change
				'special_price'         => $product->price_special,
				'additional_attributes' => array(
					'single_data' => array(
						array( 'key' => 'composition', 'value' => $product->composition, ),
						array( 'key' => 'color', 'value' => $product->color, ),
						array( 'key' => 'measurement', 'value' => $measurement, ),
						array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
						array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
					),
				),
			);

			// Creation of product simple
			$error_message = '';
			$updated_product = 0;
			try {
				$result = $proxy->catalogProductUpdate($sessionId, $sku, $productData);
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$productData['status'] = $product->isFinal ?? 2;
				$productData['visibility'] = 4;
				$productData['tax_class_id'] = 2;
				$productData['weight'] = 0;

				$result = $proxy->catalogProductCreate($sessionId, 'simple', 4, $sku, $productData);
			} else {
				$updated_product = 1;
			}
		}

		$images = $product->getMedia(config('constants.media_tags'));

		$i = 0;
		// dd($result);
		// $productId = $result;

		if ($updated_product == 1) {
			foreach ($old_images as $old_image) {
				$first_letter = substr($old_image->getBasenameAttribute(), 0, 1);
				$second_letter = substr($old_image->getBasenameAttribute(), 1, 1);
				$image_name = "/$first_letter/$second_letter/" . $old_image->getBasenameAttribute();

				try {
					$result = $proxy->catalogProductAttributeMediaRemove(
						$sessionId,
						$sku,
						$image_name
					);
				} catch (\Exception $e) {

				}
			}
		}

		foreach ($images as $image){

			$image->getUrl();

			$file = array(
				'name' => $image->getBasenameAttribute(),
				'content' => base64_encode(file_get_contents($image->getAbsolutePath())),
				'mime' => mime_content_type($image->getAbsolutePath())
			);

			$types = $i ? array('') : array('size_guide','image','small_image','thumbnail','hover_image');

			$result = $proxy->catalogProductAttributeMediaCreate(
				$sessionId,
				$sku,
				array('file' => $file, 'label' => $image->getBasenameAttribute() , 'position' => ++$i , 'types' => $types, 'exclude' => 0)
			);
		}

		if (count(explode(',', $old_sizes)) != $deleted_count) {
			return [$result, FALSE];
		} else {
			return [$result, TRUE];
		}
	}
}
