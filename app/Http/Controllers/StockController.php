<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;
use App\Setting;
use App\Product;
use App\PrivateView;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->input('orderby') == '')
          $orderby = 'asc';
      else
          $orderby = 'desc';

      $stocks = Stock::latest()->paginate(Setting::get('pagination'));

      return view('stock.index', [
        'stocks'  => $stocks,
        'orderby' => $orderby
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('stock.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'courier'     => 'required|string|min:3|max:255',
        'from'        => 'sometimes|nullable|string|min:3|max:255',
        'date'        => 'sometimes|nullable',
        'awb'         => 'required|min:3|max:255',
        'l_dimension' => 'sometimes|nullable|numeric',
        'w_dimension' => 'sometimes|nullable|numeric',
        'h_dimension' => 'sometimes|nullable|numeric',
        'weight'      => 'sometimes|nullable|numeric',
        'pcs'         => 'sometimes|nullable|numeric',
      ]);

      $stock = Stock::create($request->except('_token'));

      if ($request->ajax()) {
        return response($stock->id);
      }

      return redirect()->route('stock.index')->with('success', 'You have successfully created stock');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $stock = Stock::find($id);

      return view('stock.show', [
        'stock' => $stock
      ]);
    }

    public function trackPackage(Request $request)
    {
      $url = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=BOM07707&awb=awb&numbers=$request->awb&format=html&lickey=e2be31925a15e48125bfec50bfeb64a7&verno=1.3f&scan=1";
      // $content = $_POST['data'];
      //$content = '{"request":"{"event":"INBOX","from":"918879948245","to":"918291920455","text":"Let me know if u get this"}","response":"","status":200}';

      $curl = curl_init($url);
      // curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      // curl_setopt($curl, CURLOPT_HTTPHEADER,
      //         array("Content-type: application/json"));
      // curl_setopt($curl, CURLOPT_POST, true);
      // curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

      $response = curl_exec($curl);
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      // $params = [
      //   // 'request' => $content,
      //   'response' => $response,
      //   'status' => $status
      // ];
      // file_put_contents(__DIR__."/log.txt", json_encode($params));
      // file_put_contents(__DIR__."/status.txt", json_encode($status));
      // file_put_contents(__DIR__."/response.txt", json_encode($response));

      curl_close($curl);

      return response($response);

      // $xml = simplexml_load_string($response);

      // dd($xml);
      // dd($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'courier'     => 'required|string|min:3|max:255',
        'from'        => 'sometimes|nullable|string|min:3|max:255',
        'date'        => 'sometimes|nullable',
        'awb'         => 'required|min:3|max:255',
        'l_dimension' => 'sometimes|nullable|numeric',
        'w_dimension' => 'sometimes|nullable|numeric',
        'h_dimension' => 'sometimes|nullable|numeric',
        'weight'      => 'sometimes|nullable|numeric',
        'pcs'         => 'sometimes|nullable|numeric',
      ]);

      Stock::find($id)->update($request->except('_token'));

      return redirect()->route('stock.show', $id)->with('success', 'You have successfully updated stock!');
    }

    public function privateViewing()
    {
      $private_views = PrivateView::paginate(Setting::get('pagination'));

      return view('instock.private-viewing', [
        'private_views' => $private_views
      ]);
    }

    public function privateViewingStore(Request $request)
    {
      $products = json_decode($request->products);

      foreach ($products as $product_id) {
        $private_view = new PrivateView;
        $private_view->customer_id = $request->customer_id;
        $private_view->date = $request->date;
        $private_view->save();

        $private_view->products()->attach($product_id);

        $product = Product::find($product_id);
        $product->supplier = '';
        $product->save();
      }

      return redirect()->route('customer.show', $request->customer_id)->with('success', 'You have successfully added products for private viewing!');
    }

    public function privateViewingUpload(Request $request)
    {
      $this->validate($request, [
        'images'  => 'required'
      ]);

      $private_view = PrivateView::find($request->view_id);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $private_view->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->back()->with('success', 'You have successfully uploaded images!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Stock::find($id)->delete();

      return redirect()->route('stock.index')->with('success', 'You have successfully archived stock');
    }

    public function permanentDelete($id)
    {
      $stock = Stock::find($id);
      $stock->products()->detach();
      $stock->forceDelete();

      return redirect()->route('stock.index')->with('success', 'You have successfully deleted stock');
    }
}
