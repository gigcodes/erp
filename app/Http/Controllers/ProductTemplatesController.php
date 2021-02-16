<?php

namespace App\Http\Controllers;

use App\BroadcastImage;
use File;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\Setting;
use App\ProductTemplate;
use App\Template;
use App\Category;
use App\Product;

class ProductTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$productTemplates = \App\ProductTemplate::orderBy("id", "desc")->paginate(10);
        $images = $request->get('images', false);
        $productArr = null;
        if ($images) {
            $productIdsArr = \DB::table('mediables')
                                ->whereIn('media_id', json_decode($images))
                                ->where('mediable_type', 'App\Product')
                                ->pluck('mediable_id')
                                ->toArray();
            
            if (!empty($productIdsArr)) {
                $productArr = \App\Product::select('id', 'name', 'sku', 'brand')->whereIn('id', $productIdsArr)->get();
            }
        }
        $templateArr = \App\Template::all();

        $texts = \App\ProductTemplate::where('text',"!=" ,"")->groupBy('text')->pluck('text','text')->toArray();
        $backgroundColors = \App\ProductTemplate::where('background_color',"!=" ,"")->groupBy('background_color')->pluck('background_color','background_color')->toArray();

        return view("product-template.index", compact('templateArr', 'productArr', 'texts' , 'backgroundColors'));
    }

    public function response()
    {
        $keyword = request('keyword');

        $records = \App\ProductTemplate::leftJoin('brands as b','b.id','product_templates.brand_id')->leftJoin("store_websites as sw","sw.id","product_templates.store_website_id"); 

        if(!empty($keyword)) {
            $records = $records->where(function($q) use($keyword) {
                $q->orWhere('product_templates.product_title','like','%'.$keyword.'%')->orWhere('product_templates.text','like','%'.$keyword.'%')->orWhere('product_templates.product_id','like','%'.$keyword.'%');
            });
        }
        $records = $records->orderBy("id", "desc")
        ->select(["product_templates.*","b.name as brand_name","sw.title as website_name"])
        ->paginate(Setting::get('pagination')); 

        return response()->json([
            "code" => 1,
            "result" => $records,
            "pagination" => (string)$records->appends(request()->except('page')),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template = new \App\ProductTemplate;
        $params = request()->all();
        if(empty($params['product_id'])) {
           $params['product_id'] = [];
        }
        $params['product_id'] = implode(',', (array)$params['product_id']);
        if(isset($params['background_color']) && is_array($params['background_color'])) {
            $params['background_color'] = implode(',', (array)$params['background_color']);
        }

        $template->fill($params);

        if ($template->save()) {

            if (!empty($request->get('product_media_list')) && is_array($request->get('product_media_list'))) {
                foreach ($request->get('product_media_list') as $mediaid) {
                    $media = Media::find($mediaid);
                    $template->attachMedia($media, ['template-image']);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('product-template-images')->upload();
                    $template->attachMedia($media,['template-image']);
                }
            }
        }

        return response()->json(["code" => 1, "message" => "Product Template Created successfully!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = \App\ProductTemplate::where("id", $id)->first();

        if ($template) {
            $template->delete();
        }

        return response()->json(["code" => 1, "message" => "Product Template Deleted successfully!"]);
    }

    /**
     * @SWG\Get(
     *   path="/product-template",
     *   tags={"Product Template"},
     *   summary="Get Product Template",
     *   operationId="get-product-template",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function apiIndex(Request $request)
    {
        $record = \App\ProductTemplate::latest()->first();


        if(!$record) {
            $data = ['message' => 'Template not found'];
            return response()->json($data);
        }
        
        if($record->category) {
            $category = $record->category;
            // Get other information related to category
            $cat = $category->title;
        }

        $parent = '';
        $child = '';

        try {
            if ($cat != 'Select Category') {
                if ($category->isParent($category->id)) {
                    $parent = $cat;
                    $child = $cat;
                } else {
                    $parent = $category->parent()->first()->title;
                    $child = $cat;
                }
            }
        } catch (\ErrorException $e) {
            //
        }
        $productCategory = $parent.' '.$child;

        $data = [];
        //check if template exist
        $templateProductCount = $record->template->no_of_images;
        
        // if($record->getMedia('template-image')->count() <= $templateProductCount && $templateProductCount > 0){
        //     $data = ['message' => 'Template Product Doesnt have Proper Images'];
        //     return response()->json($data);
        // }

        $record->is_processed = 2;
        $record->save();
        
        if ($record) {
            $data = [
                "id" => $record->id,
                "templateNumber" => $record->template_no,
                "productTitle" => $record->product_title,
                "productBrand" => ($record->brand) ? $record->brand->name : "",
                "productCategory" => $productCategory,
                "productPrice" => $record->price,
                "productDiscountedPrice" => $record->discounted_price,
                "productCurrency" => $record->currency,
                "text" => $record->text,
                "fontStyle" => $record->font_style,
                "fontSize" => $record->font_size,
                "backgroundColor" => explode(",", $record->background_color),
                "logo" => ($record->storeWebsite) ? $record->storeWebsite->icon : ""
            ];

            if ($record->hasMedia('template-image')) {
                $images = [];
                foreach ($record->getMedia('template-image') as $i => $media) {
                    $images[] = $media->getUrl();
                }
                $data[ "image" ] = $images;
            }
        }

        return response()->json($data);

    }

    /**
     * @SWG\Post(
     *   path="/product-template",
     *   tags={"Product Template"},
     *   summary="Save Product Template",
     *   operationId="save-product-template",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function apiSave(Request $request)
    {
        // Try to get ID from 'product_id' (this will be changed to id)
        $id = $request->post("product_id", 0);

        // Try to get ID from 'id' if no id is set
        if ( (int) $id == 0 ) {
            $id = $request->post("id", 0);
        }

        // Only do queries if we have an id
        if ( (int) $id > 0 ) {
            $template = \App\ProductTemplate::where("id", $id)->first();

            if ($template) {
                if ($request->post('image')) {
                    $image = base64_decode($request->post('image'));
                    $media = MediaUploader::fromString($image)->toDirectory(date('Y/m/d'))->useFilename('product-template-' . $id)->upload();
                    $template->attachMedia($media,'template-image');
                    $template->is_processed = 1;
                    $template->save();

                    // Store as broadcast image
                    $broadcastImage = new BroadcastImage();
                    $broadcastImage->products = '[' . $template->product_id . ']';
                    $broadcastImage->save();
                    $broadcastImage->attachMedia($media, config('constants.media_tags'));

                    //Save Product For Image In Mediable
                    if($template->product_id != null){
                        $product = Product::find($template->product_id);
                        $tag = 'template_'.$template->template_no;
                        $product->attachMedia($media, $tag);
                    }
                    
                    return response()->json(["code" => 1, "message" => "Product template updated successfully"]);
                }
            } else {
                return response()->json(["code" => 0, "message" => "Sorry, can not find product template in record"]);
            }
        }

        return response()->json(["code" => 0, "message" => "An unknown error has occured"]);

    }

    /**
     * Show the image for selecting product id.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectProductId(Request $request)
    {
        $html = '';
        $productId = $request->get('product_ids');
        if ($productId) {
            $productArr = \App\Product::whereIn('id', $productId)->get();
            if ($productArr) {
                foreach ($productArr as $product) {
                    foreach ($product->media as $k => $media) {
                        $html .= '<div class="col-sm-3" style="padding-bottom: 10px;">
                                    <div class="imagePreview">
                                        <img src="' . $media->getUrl() . '" width="100%" height="100%">
                                    </div>
                                    <label class="btn btn-primary">
                                        <input type="checkbox" name="product_media_list[]" value="' . $media->id . '" class="product_media_list"> Select
                                    </label>
                                </div>';
                    }
                }
            }
        }
        return response()->json(["data" => $html]);
    }

    public function imageIndex(Request $request)
    {
        $temps = Template::all();
        if($request->template || $request->brand || $request->category){
            
            $query = ProductTemplate::query();

            if(!empty($request->template)){
                $query->where('template_no',$request->template);
            }
            
            if(!empty($request->brand)){
                $query->whereIn('brand_id',$request->brand);
            }
            
            if(!empty($request->category && $request->category[0] != 1)){
                $query->whereIn('category_id',$request->category);
            }

            $range = explode(' - ', request('date_range'));

            if($range[0] == end($range)){
                $query->whereDate('updated_at', end($range));
            }else{
                $start = str_replace('/', '-', $range[0]);
                $end = str_replace('/', '-', end($range));
                $query->whereBetween('updated_at', array($start,$end));
            }
            
            $templates = $query->where('is_processed',1)->orderBy('updated_at','desc')->paginate(Setting::get('pagination'))->appends(request()->except(['page']));
        }else{
           $templates = ProductTemplate::where('is_processed',1)->orderBy('updated_at','desc')->paginate(Setting::get('pagination')); 
        }
        
        // if ($request->ajax()) {
        //     return response()->json([
        //         'tbody' => view('product-template.partials.type-list-template', compact('templates','temps'))->render(),
        //         'links' => (string)$templates->render(),
        //         'total' => $templates->total(),
        //     ], 200);
        // }

        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        return view('product-template.image',compact('templates','temps','category_selection'));
    }
}
