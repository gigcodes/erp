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
        return view("template.index");
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
           $templates = ProductTemplate::where('type',1)->paginate(Setting::get('pagination')); 
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
        $numbers = ['1','2','3','4','6'];
        foreach ($numbers as $number) {
            $template = Template::where('no_of_images',$number)->first();
            $categories = Category::select('id')->get();
                foreach ($categories as $category) {
                $brands = Brand::select('id')->get();
                foreach ($brands as $brand) {
                   $products = Product::where('category',$category->id)->where('brand',$brand->id)->latest()->limit(50)->get();
                   if($number == 1){
                       foreach ($products as $product) {
                          if($product->getMedia(config('constants.media_tags'))->count() != 0){
                                if($template != null){
                                    $productTemplate = new ProductTemplate;
                                    $productTemplate->template_no = $template->id;
                                    $productTemplate->product_title = $product->name;
                                    $productTemplate->category_id = $product->category;
                                    $productTemplate->brand_id = $product->brand;
                                    $productTemplate->currency = 'eur';
                                    if(empty($product->price)){
                                        $product->price = 0;
                                    }
                                    if(empty($product->price_eur_discounted)){
                                        $product->price_eur_discounted = 0;
                                    }
                                    $productTemplate->price = $product->price;
                                    $productTemplate->discounted_price = $product->price_eur_discounted; 
                                    $productTemplate->product_id = $product->id;
                                    $productTemplate->is_processed = 0;
                                    $productTemplate->type = 1;
                                    $productTemplate->save();
                                    foreach ($product->getMedia(config('constants.media_tags'))->all() as $media) {
                                        $media = Media::find($media->id);
                                        $tag = 'template-image';
                                        $productTemplate->attachMedia($media, $tag);
                                    }
                                }
                                
                            }
                       }
                    }

                    if($number == 2){
                        $count = 0;
                        foreach ($products as $product) {
                            if($product->getMedia(config('constants.media_tags'))->count() != 0){
                                if($count == 2){
                                    $count = 0;
                                }

                                if($count == 0){
                                    $productTemplate = new ProductTemplate;
                                    $productTemplate->template_no = $template->id;
                                    $productTemplate->product_title = '';
                                    $productTemplate->brand_id = $product->brand;
                                    $productTemplate->currency = 'eur';
                                    $productTemplate->price = '';
                                    $productTemplate->category_id = $product->category;
                                    $productTemplate->discounted_price = ''; 
                                    $productTemplate->product_id = '';
                                    $productTemplate->is_processed = 0;
                                    $productTemplate->type = 1;
                                    $productTemplate->save();
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    $productTemplate->attachMedia($media, $tag);

                                }

                                if($count == 1){
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    $productTemplate = ProductTemplate::find($productTemplate->id);
                                    $productTemplate->attachMedia($media, $tag);
                                }

                                $count++;

                            }    
                       }
                    }

                    if($number == 3){
                        $count = 0;
                        foreach ($products as $product) {
                            if($product->getMedia(config('constants.media_tags'))->count() != 0){
                                if($count == 3){
                                    $count = 0;
                                }

                                if($count == 0){
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
                                    $productTemplate->attachMedia($media, $tag);

                                }

                                if($count == 1 || $count == 2){
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    $productTemplate = ProductTemplate::find($productTemplate->id);
                                    $productTemplate->attachMedia($media, $tag);
                                }

                                $count++;

                            }    
                       }
                    } 

                    if($number == 4){
                        $count = 0;
                        foreach ($products as $product) {
                            if($product->getMedia(config('constants.media_tags'))->count() != 0){
                                if($count == 4){
                                    $count = 0;
                                }

                                if($count == 0){
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
                                    $productTemplate->attachMedia($media, $tag);

                                }

                                if($count == 1 || $count == 2 || $count == 3){
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag = 'template-image';
                                    $productTemplate = ProductTemplate::find($productTemplate->id);
                                    $productTemplate->attachMedia($media, $tag);
                                }

                                $count++;

                            }    
                       }
                    }  


                }
            } 
        }
        
        return response()->json(["message" => "Sucess"],200);
    }

}
