<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productTemplates = \App\ProductTemplate::orderBy("id", "desc")->paginate(10);
        return view("product-template.index");
    }

    public function response()
    {
        $records = \App\ProductTemplate::orderBy("id", "desc")->paginate(5);
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
        $template      = new \App\ProductTemplate;
        
        $template->fill(request()->all());

        if ($template->save()) {
            
            if (!empty($request->get('product_media_list')) && is_array($request->get('product_media_list'))) {
                foreach ($request->get('product_media_list') as $mediaid) {
                    $media = Media::find($mediaid);
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('product-template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return response()->json(["code" => 1, "message" => "Product Template Created successfully!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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

    public function apiIndex(Request $request)
    {
        $limit   = $request->get("limit", 10);
        $records = \App\ProductTemplate::leftJoin("brands as b", "b.id", "product_templates.brand_id")
            ->select(["product_templates.*", "b.name as brand_name"]);

        if ($request->get("id", null) != null) {
            $records->where("id", $request->get("id"));
        }

        if ($request->get("productTitle", null) != null) {
            $q = $request->get('productTitle');
            $records->where("product_title", "like", "%$q%");
        }

        if ($request->get("productBrand", null) != null) {
            $q = $request->get('productBrand');
            $records->where("b.name", "like", "%$q%");
        }

        $records->where("product_templates.is_processed", "=", "1");

        $record = $records->orderBy("product_templates.id", "asc")->first();
        $data = [];
        if ($record) {
            $data = [
                "id"                     => $record->id,
                "templateNumber"         => $record->template_no,
                "productTitle"           => $record->product_title,
                "productBrand"           => $record->brand_name,
                "productPrice"           => $record->price,
                "productDiscountedPrice" => $record->discounted_price,
                "productCurrency"        => $record->currency,
            ];

            if ($record->hasMedia(config('constants.media_tags'))) {
                foreach ($record->getMedia(config('constants.media_tags')) as $i => $media) {
                    $data["image" . ($i + 1)] = $media->getUrl();
                }
            }
        }

        return response()->json($data);

    }

    public function apiSave(Request $request)
    {
        $id = $request->get("id", 0);

        $template = \App\ProductTemplate::where("id", $id)->first();
        if ($template) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('product-template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }

                $template->is_processed = 1;
                $template->save();

                return response()->json(["code" => 1, "message" => "Product template updated successfully"]);
            }
        }

        return response()->json(["code" => 0, "message" => "Sorry, can not find product template in record"]);

    }
    
    /**
     * Show the image for selecting product id.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectProductId(Request $request)
    {
        $html = '';
        $productId = $request->get('product_id');
        if ($productId) {
            $product = \App\Product::where('id', $productId)->first();
            if ($product) {
                foreach ($product->media as $k => $media) {
                    $html .= '<div class="col-sm-3" style="padding-bottom: 10px;">
                                <div class="imagePreview">
                                    <img src="'.$media->getUrl().'" width="100%" height="100%">
                                </div>
                                <label class="btn btn-primary">
                                    <input type="checkbox" name="product_media_list[]" value="'.$media->id.'"> Select
                                </label>
                            </div>';
                }
            }
        }
        return response()->json(["data" => $html]);
    }
}
