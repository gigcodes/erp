<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\googleTraslationSettings;

class GoogleTraslationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings = googleTraslationSettings::query();
        // $settings = googleTraslationSettings::all();

        if ($request->term) {
            $settings->where(function ($q) use ($request) {
                $q = $q->orWhere('email', 'LIKE', '%' . $request->term . '%')
                  ->orWhere('account_json', 'LIKE', '%' . $request->term . '%')
                  ->orWhere('last_note', 'LIKE', '%' . $request->term . '%')
                  ->orWhere('project_id', 'LIKE', '%' . $request->term . '%');
            });
        }

        $settings = $settings->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleTraslationSettings.list', compact('settings'))->render(),
                // 'links' => (string) $data->render(),
                // 'count' => $data->total(),
            ], 200);
        }

        return view('googleTraslationSettings.index', compact('settings'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'last_note' => 'required',
                'status' => 'required|boolean',
                'account_json' => 'required',
                'project_id' => 'required',
            ]);

            $email = $request->email;
            $account_json = $request->account_json;
            $status = $request->status;
            $last_note = $request->last_note;
            $project_id = $request->project_id;

            $googleTraslationSettings = new googleTraslationSettings;

            $googleTraslationSettings->email = $email;
            $googleTraslationSettings->account_json = $account_json;
            $googleTraslationSettings->status = $status;
            $googleTraslationSettings->last_note = $last_note;
            $googleTraslationSettings->project_id = $project_id;
            $googleTraslationSettings->save();

            $msg = 'Setting Add Successfully';

            return redirect()->route('google-traslation-settings.index')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->route('google-traslation-settings.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(googleTraslationSettings $googleTraslationSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id, googleTraslationSettings $googleTraslationSettings)
    {
        $data = googleTraslationSettings::where('id', $id)->first();

        return view('googleTraslationSettings.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, googleTraslationSettings $googleTraslationSettings)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'last_note' => 'required',
            'status' => 'required|boolean',
            'account_json' => 'required',
            'project_id' => 'required',
        ]);
        try {
            $id = $request->id;
            $email = $request->email;
            $account_json = $request->account_json;
            $status = $request->status;
            $last_note = $request->last_note;
            $project_id = $request->project_id;

            $googleTraslationSettings = new googleTraslationSettings;
            $googleTraslationSettings->where('id', $id)
            ->limit(1)
            ->update([
                'email' => $email,
                'account_json' => $account_json,
                'status' => $status,
                'last_note' => $last_note,
                'project_id' => $project_id,
            ]);

            return redirect()->route('google-traslation-settings.index')->with('success', 'Setting Update Successfully');
        } catch (Exception $e) {
            return redirect()->route('google-traslation-settings.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\googleTraslationSettings  $googleTraslationSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $googleTraslationSettings)
    {
        googleTraslationSettings::where('id', $googleTraslationSettings->setting)->delete();

        $msg = 'Setting Delete Successfully';

        return redirect()->route('google-traslation-settings.index')->with('success', $msg);
    }
}
