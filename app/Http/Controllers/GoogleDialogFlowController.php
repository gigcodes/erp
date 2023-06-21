<?php

namespace App\Http\Controllers;

use App\Models\GoogleDialogAccount;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Plank\Mediable\Facades\MediaUploader;

class GoogleDialogFlowController extends Controller
{

    /**
     * Get all the dialogflow accounts
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $google_dialog_accounts = GoogleDialogAccount::with(['storeWebsite'])->orderBy('id', 'desc')->get();
        $store_websites = StoreWebsite::all();
        return view('google-dialogflow.index', compact('google_dialog_accounts', 'store_websites'));
    }

    /**
     * Create a new account in ERP with client Id & Secret.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = \Validator::make($request->all(), [
                'site_id' => 'required|integer',
                'project_id' => 'required|string',
                'service_file' => 'required|mimes:json',
            ]);
            if ($validator->fails()) {
                return Redirect::route('google-chatbot-accounts')->withInput()->withErrors($validator);
            }
            $serviceFile = MediaUploader::fromSource($request->file('service_file'))
                ->toDirectory('googleDialogService/')->upload();
            if ($request->get('default_account')) {
                $defaultAccount =  GoogleDialogAccount::where('default_selected', true)->update(['default_selected' => false]);
            }
            GoogleDialogAccount::create([
                'service_file' => $serviceFile->getAbsolutePath(),
                'site_id' => $request->get('site_id'),
                'project_id' => $request->get('project_id'),
                'default_selected' => $request->get('default_account')
            ]);
            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account added successfully!');
        } catch (\Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Update a account in ERP with client Id & Secret.
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $validator = \Validator::make($request->all(), [
                'account_id' => 'required|integer',
                'edit_site_id' => 'required|integer',
                'edit_project_id' => 'required|string',
                'edit_service_file' => 'sometimes|mimes:json',
            ]);
            if ($validator->fails()) {
                return Redirect::route('google-chatbot-accounts')->withInput()->withErrors($validator);
            }
            $googleAccount = GoogleDialogAccount::where('id', $request->get('account_id'))->first();
            if (!$googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            if ($request->get('default_account')) {
               $defaultAccount =  GoogleDialogAccount::where('default_selected', true)->update(['default_selected' => false]);
            }
            $googleAccount->site_id = $request->get('edit_site_id');
            $googleAccount->project_id = $request->get('edit_project_id');
            $googleAccount->default_selected = $request->get('default_account');
            if ($request->hasFile('edit_service_file')) {
                $serviceFile = MediaUploader::fromSource($request->file('edit_service_file'))
                    ->toDirectory('googleDialogService/')->upload();
                $googleAccount->service_file = $serviceFile->getAbsolutePath();
            }
            $googleAccount->save();
            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account updated successfully!');
        } catch (\Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a account in ERP with client Id & Secret.
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request, $id): RedirectResponse
    {
        try {
            $googleAccount = GoogleDialogAccount::where('id', $id)->first();
            if (!$googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleAccount->delete();
            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account deleted successfully!');
        } catch (\Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Get account details
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request, $id): JsonResponse
    {
        try {
            $googleAccount = GoogleDialogAccount::where('id', $id)->first();
            if (!$googleAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            return response()->json(['status' => true, 'message' => 'google dialog account deleted successfully!', 'data' => $googleAccount->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
