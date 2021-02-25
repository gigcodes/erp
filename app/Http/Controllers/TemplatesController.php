<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Setting;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\Template;
use App\Product;
use App\Category;
use App\Brand;
use App\ProductTemplate;
use Plank\Mediable\Media;
use Illuminate\Support\Str;
use App\Helpers\GuzzleHelper;


use DB;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates=collect(self::bearBannerTemplates());

        return view("template.index",compact('templates'));
    }

    public function response()
    {
        $records = \App\Template::orderBy("id", "desc")->paginate(Setting::get('pagination'));
        foreach($records as &$item) {
            $media = $item->lastMedia(config('constants.media_tags'));
            $item->image = ($media) ? $media->getUrl() : "";
        }
        return response()->json([
            "code"       => 1,
            "result"     => $records,
            "pagination" => (string) $records->links(),
        ]);
    }

    public function BearBannerList()
    {
        $records = \App\Template::orderBy("id", "desc")->paginate(Setting::get('pagination'));
        foreach($records as &$item) {
            $media = $item->lastMedia(config('constants.media_tags'));
            $item->image = ($media) ? $media->getUrl() : "";
        }
        return response()->json([
            "code"       => 1,
            "result"     => $records,
            "pagination" => (string) $records->links(),
        ]);
    }

    static function bearBannerTemplates()
    {
        $url=env('BANNER_API_LINK').'/templates';

        $api_key=env('BANNER_API_KEY');

        //echo $api_key;die;

        $headers=   [
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type' => 'application/json'
                    ];

       $response=GuzzleHelper::get($url,$headers);

       return $response;
    }


    public function viewTemplate()
    {
       $url=env('BANNER_API_LINK').'/templates/3g8zka5Yz6rDEJXBYm';

        $api_key=env('BANNER_API_KEY');

        //echo $api_key;die;

        $headers=   [
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type' => 'application/json'
                    ];

       $response=GuzzleHelper::get($url,$headers);

       echo '<pre>';print_r($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template = new \App\Template;
        if($request->auto_generate_product == 'on'){
           $request->merge(['auto_generate_product' => '1']);
        }
        
        $template->fill(request()->all());

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return response()->json(["code" => 1, "message" => "Template Created successfully!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = \App\Template::where("id", $id)->first();

        if ($template) {
            $template->delete();
        }

        return response()->json(["code" => 1, "message" => "Template Deleted successfully!"]);
    }

    public function edit(Request $request)
    {
        $template = \App\Template::find($request->id);
        if($request->auto == 'on'){
           $template->auto_generate_product = 1;
        }else{
            $template->auto_generate_product = 0;
        }
        $template->name = $request->name;
        $template->no_of_images = $request->number;
        $template->update();

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return redirect()->back();
    
    }


    public function typeIndex(Request $request)
    {
        $temps = Template::all();
        if($request->search){
            $templates = ProductTemplate::where('template_no',$request->search)->paginate(Setting::get('pagination'))->appends(request()->except(['page']));
        }else{
           $templates = ProductTemplate::paginate(Setting::get('pagination')); 
        }
        
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('product-template.partials.type-list-template', compact('templates','temps'))->render(),
                'links' => (string)$templates->render(),
                'total' => $templates->total(),
            ], 200);
        }

        
        return view('product-template.type-index',compact('templates','temps'));
    }

    public function generateTempalateCategoryBrand()
    {
        $templates = Template::where('auto_generate_product',1)->get();
        foreach ($templates as $template) {
            $categories = Category::select('id')->get();
                foreach ($categories as $category) {
                $brands = Brand::select('id')->get();
                foreach ($brands as $brand) {
                   $products = Product::where('category',$category->id)->where('brand',$brand->id)->latest()->limit(50)->get();
                   foreach ($products as $product) {
                        if($product->getMedia(config('constants.media_tags'))->count() != 0){
                            $oldTemplate = ProductTemplate::where('template_no',$template->id)->where('type',1)->orderBy('id','desc')->first();
                            if($oldTemplate != null){
                                $mediable = DB::table('mediables')->where('mediable_type','App\ProductTemplate')->where('mediable_id',$oldTemplate->id)->count();
                                if($template->no_of_images == $mediable){
                                    //check if Product Template Already Exist
                                    $temp = ProductTemplate::where('template_no',$template->id)->where('brand_id',$product->brand)->where('category_id',$product->category)->where('is_processed',0)->where('type',1)->count();
                                    
                                    if($temp == 0){
                                        $productTemplate = new ProductTemplate;
                                        $productTemplate->template_no = $template->id;
                                        $productTemplate->product_title = '';
                                        $productTemplate->brand_id = $product->brand;
                                        $productTemplate->currency = 'eur';
                                        $productTemplate->price = '';
                                        $productTemplate->discounted_price = ''; 
                                        $productTemplate->category_id = $product->category;
                                        $productTemplate->product_id = '';
                                        $productTemplate->is_processed = 0;
                                        $productTemplate->type = 1;
                                        $productTemplate->save();
                                        $media = $product->getMedia(config('constants.media_tags'))->first();
                                        $media = Media::find($media->id);
                                        $tag = 'template-image';
                                        try {
                                           $productTemplate->attachMedia($media, $tag); 
                                        } catch (\Exception $e) {
                                            continue;
                                        }
                                    }    
                                    
                                }else{
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    $oldTemplate->attachMedia($media, $tag);   
                               }
                            }else{
                                //check if Product Template Already Exist
                                $temp = ProductTemplate::where('template_no',$template->id)->where('brand_id',$product->brand)->where('category_id',$product->category)->where('is_processed',0)->where('type',1)->count();
                                if($temp == 0){
                                    $productTemplate = new ProductTemplate;
                                    $productTemplate->template_no = $template->id;
                                    $productTemplate->product_title = '';
                                    $productTemplate->brand_id = $product->brand;
                                    $productTemplate->currency = 'eur';
                                    $productTemplate->price = '';
                                    $productTemplate->discounted_price = ''; 
                                    $productTemplate->category_id = $product->category;
                                    $productTemplate->product_id = '';
                                    $productTemplate->is_processed = 0;
                                    $productTemplate->type = 1;
                                    $productTemplate->save();
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    try {
                                        $productTemplate->attachMedia($media, $tag); 
                                    } catch (\Exception $e) {
                                        continue;
                                    }
                                }    
                            }
                        }
                   }
                }
            } 
        }
        
        return response()->json(["message" => "Sucess"],200);
    }
    public function getTemplateProduct(request $request){
        $id = $request->input('productid');
        $productData = product::find($id);
        $image = $productData->getMedia(\Config('constants.media_original_tag'))->first(); 
        $responseData = [
            'status'=>'success',
            'productName'=>$productData->name,
            'short_description'=>Str::limit($productData->short_description, 20, $end='...'),
            'price'=>'$'.$productData->price,
            'product_url'=>'www.test.com',
        ];
        if($image) { 
            $responseData['product_image'] = $image->getUrl(); 
        }
        if(isset($productData)){
            return response()->json($responseData);
        }
        return response()->json(['status'=>'failed','message'=>'Product not found']);
    }
}
