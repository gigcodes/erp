<?php

namespace App\Http\Controllers;

use App\CroppedImageReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Category;
use App\Helpers\StatusHelper;
use App\Product;

class CroppedImageReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = CroppedImageReference::with(['media', 'newMedia'])->orderBy('id', 'desc')->paginate(50);

        return view('image_references.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function show(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function edit(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CroppedImageReference $croppedImageReference)
    {
        //
    }

    public function grid(Request $request)
    {
        
        $query = CroppedImageReference::query();
            if($request->category || $request->brand || $request->supplier || $request->crop || $request->status){
                

                if(is_array(request('category'))){
                    if (request('category') != null && request('category')[0] != 1){ 
                        $query->whereHas('product', function ($qu) use ($request) {
                                $qu->whereIn('category', request('category'));
                            });
                    }
                }else{
                    if (request('category') != null && request('category') != 1){ 
                            $query->whereHas('product', function ($qu) use ($request) {
                                $qu->where('category', request('category'));
                            });
                    }
                }
                
                if (request('brand') != null){ 
                 $query->whereHas('product', function ($qu) use ($request) {
                                $qu->whereIn('brand', request('brand'));
                            });
                }

                if (request('supplier') != null){ 
                 $query->whereHas('product', function ($qu) use ($request) {
                                $qu->whereIn('supplier', request('supplier'));
                            });
                }

                if (request('status') != null && request('status') != 0){ 
                 $query->whereHas('product', function ($qu) use ($request) {
                                $qu->where('status_id', request('status'));
                 });
                }else{
                  $query->whereHas('product', function ($qu) use ($request) {
                                $qu->where('status_id','!=',StatusHelper::$cropRejected);
                            });   
                }

                if (request('crop') != null){
                    if(request('crop') == 2){
                      $query->whereNotNull('new_media_id'); 
                    }elseif(request('crop') == 3){
                      $query->whereNull('new_media_id');  
                    }
                } 
            $products = $query->orderBy('id', 'desc')->paginate(50); 

        }else{

            $query->whereHas('product', function ($qu) use ($request) {
                                $qu->where('status_id','!=',StatusHelper::$cropRejected);
                            }); 
            $products = $query->orderBy('id', 'desc')->groupBy('original_media_id')->paginate(50);

        }
        
        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2','id' => 'category'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        $total = $query->count(); 
         
         $pendingProduct = Product::where('status_id',StatusHelper::$autoCrop)->where('stock','>=',1)->count();

         $pendingCategoryProduct = Product::where('status_id',StatusHelper::$attributeRejectCategory)->where('stock','>=',1)->count();  
        
         if (request('customer_range') != null){
                   $dateArray =  explode('-',request('customer_range'));
                   $startDate = trim($dateArray[0]);
                   $endDate = trim(end($dateArray));
                   if($startDate == '1995/12/25'){
                      $totalCounts = CroppedImageReference::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
                   }
                   elseif($startDate == $endDate){

                      $totalCounts = CroppedImageReference::whereDate('created_at', '=', end($dateArray))->count();  
                   }else{
                     $totalCounts = CroppedImageReference::whereBetween('created_at', [$startDate, $endDate])->count();     
                   }

                   if ($request->ajax()) {
                    return response()->json([
                        'count' => $totalCounts
                    ], 200);
                    }
        }else{
            $totalCounts = 0;
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('image_references.partials.griddata', compact('products','category_selection','total','pendingProduct','totalCounts','pendingCategoryProduct'))->render(),
                'links' => (string)$products->render(),
                'total' => $total,
            ], 200);
            }
           
        return view('image_references.grid', compact('products','category_selection','total','pendingProduct','totalCounts','pendingCategoryProduct'));
    }

    public function rejectCropImage(Request $request)
    {
        $reference = CroppedImageReference::find($request->id);
        $product = Product::find($reference->product_id);
        dd($product);
    }
}
