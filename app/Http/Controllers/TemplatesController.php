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
        $categories = Category::select('id')->get(); 
        foreach ($categories as $category) {
            $brands = Brand::select('id')->get();
            foreach ($brands as $brand) {
               $products = Product::where('category',$category->id)->where('brand',$brand->id)->latest()->limit(50)->get();
               foreach ($products as $product) {
                  if($product->getMedia(config('constants.media_tags'))->count() != 0){
                        
                        $template = Template::where('no_of_images',$product->getMedia(config('constants.media_tags'))->count())->first();
                        if($template != null){
                            $productTemplate = new ProductTemplate;
                            $productTemplate->template_no = $template->id;
                            $productTemplate->product_title = $product->name;
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
        }

        return response()->json(["message" => "Sucess"],200);
    }

}
