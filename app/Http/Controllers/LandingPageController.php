<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\LandingPageProduct;

class LandingPageController extends Controller
{

  public function __construct()
  {

  }

  public function index(Request $request)
  {
     $title = "Landing Page";
     $status = \App\LandingPageProduct::STATUS;
     return view("landing-page.index",compact(['title','status']));
  }

  public function records(Request $request) 
  {
      $records = \App\LandingPageProduct::query();

      $keyword = request("keyword");
      if (!empty($keyword)) {
          $records = $records->where(function ($q) use ($keyword) {
              $q->where("product_id", "LIKE", "%$keyword%");
          });
      }

      $records = $records->get();

      foreach ($records as &$rec) {
          $rec->status_name = isset(\App\LandingPageProduct::STATUS[$rec->status]) ? \App\LandingPageProduct::STATUS[$rec->status] : $rec->status;
      }

      return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
  }

  public function save(Request $request)
  {
      $params = $request->all();
      $productIds = json_decode($request->get("images"),true);

      if(!empty($productIds)) {
          foreach($productIds as $productId) {
              LandingPageProduct::updateOrCreate(["product_id" => $productId],["product_id" => $productId]);
          }
      }

      return redirect()->route('landing-page.index')->withSuccess('You have successfully added landing page!');
  }

  public function store(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'product_id' => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $id = $request->get("id", 0);

        $records = LandingPageProduct::find($id);

        if (!$records) {
            $records = new LandingPageProduct;
        }

        $records->fill($post);
        $records->save();

        return response()->json(["code" => 200, "data" => $records]);

    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $landingPage = LandingPageProduct::where("id", $id)->first();

        if ($landingPage) {
            return response()->json(["code" => 200, "data" => $landingPage]);
        }

        return response()->json(["code" => 500, "error" => "Wrong row id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $landingPage = LandingPageProduct::where("id", $id)->first();

        if ($landingPage) {
            $landingPage->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong row id!"]);
    }

}