<?php

namespace App\Http\Controllers;

use App\DigitalMarketingPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DigitalMarketingController extends Controller
{
    public function index(Request $request)
    {
        $title  = "Social-Digital Marketing";
        $status = \App\DigitalMarketingPlatform::STATUS;

        return view("digital-marketing.index", compact('records', 'title', 'status'));
    }

    public function records(Request $request)
    {
        $records = \App\DigitalMarketingPlatform::query();

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("platform", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        foreach ($records as &$rec) {
            $rec->status_name = isset(\App\DigitalMarketingPlatform::STATUS[$rec->status]) ? \App\DigitalMarketingPlatform::STATUS[$rec->status] : $rec->status;
        }

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    public function save(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'platform'    => 'required',
            'description' => 'required',
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

        $records = DigitalMarketingPlatform::find($id);

        if (!$records) {
            $records = new DigitalMarketingPlatform;
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
        $digitalMarketing = DigitalMarketingPlatform::where("id", $id)->first();

        if ($digitalMarketing) {
            return response()->json(["code" => 200, "data" => $digitalMarketing]);
        }

        return response()->json(["code" => 500, "error" => "Wrong digital marketing id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $digitalMarketing = DigitalMarketingPlatform::where("id", $id)->first();

        if ($digitalMarketing) {
            $digitalMarketing->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong digital marketing id!"]);
    }

    public function solution(Request $request, $id)
    {
        $title = "Social-Digital Marketing Solution";

        return view("digital-marketing.solution.index", compact('title', 'status', 'id'));

    }

    public function solutionRecords(Request $request, $id)
    {

        $records = \App\DigitalMarketingSolution::where("digital_marketing_platform_id", $id);

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("platform", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);

    }

    public function solutionSave(Request $request, $id)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'provider' => 'required',
            'website'  => 'required',
            'contact'  => 'required',
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

        $solutionId = $request->get("solution_id", 0);

        $records = \App\DigitalMarketingSolution::where("digital_marketing_platform_id", $id)->where("id", $solutionId)->first();

        if (!$records) {
            $records = new \App\DigitalMarketingSolution;
        }

        $records->fill($post);
        $records->digital_marketing_platform_id = $id;
        $records->save();

        return response()->json(["code" => 200, "data" => $records]);

    }

    public function solutionEdit(Request $request, $id, $solutionId)
    {
        $record = \App\DigitalMarketingSolution::where("digital_marketing_platform_id", $id)->where("id", $solutionId)->first();

        if ($record) {
            return response()->json(["code" => 200, "data" => $record]);
        }

        return response()->json(["code" => 500, "error" => "Wrong digital marketing solution id!"]);

    }

    public function solutionDelete(Request $request, $id, $solutionId)
    {
        $record = \App\DigitalMarketingSolution::where("digital_marketing_platform_id", $id)->where("id", $solutionId)->first();

        if ($record) {
            $record->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong digital marketing id!"]);
    }

}
