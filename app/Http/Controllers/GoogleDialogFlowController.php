<?php

namespace App\Http\Controllers;

use App\Library\Google\DialogFlow\DialogFlowService;
use App\Models\GoogleDialogAccount;
use App\Models\GoogleDialogAccountMails;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GoogleDialogFlowController extends Controller
{

    /**
     * Get all the dialogflow accounts
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $google_dialog_accounts = GoogleDialogAccount::with(['storeWebsite', 'accounts'])->orderBy('id', 'desc')->get();
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
                'google_client_id' => 'required|string',
                'google_client_secret' => 'required|string',
            ]);
            if ($validator->fails()) {
                return Redirect::route('google-chatbot-accounts')->withInput()->withErrors($validator);
            }
            GoogleDialogAccount::create($request->all());
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
                'site_id' => 'required|integer',
                'google_client_id' => 'required|string',
                'google_client_secret' => 'required|string',
            ]);
            if ($validator->fails()) {
                return Redirect::route('google-chatbot-accounts')->withInput()->withErrors($validator);
            }
            $googleAccount = GoogleDialogAccount::where('id', $request->get('account_id'))->first();
            if (!$googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleAccount->site_id = $request->get('site_id');
            $googleAccount->google_client_id = $request->get('google_client_id');
            $googleAccount->google_client_secret = $request->get('google_client_secret');
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
            GoogleDialogAccountMails::where('google_dialog_account_id', $id)->delete();
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

    /**
     * Connect an account to create / access the dialogflow.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function connectAccount(Request $request, $id): RedirectResponse
    {
        $googleAccount = GoogleDialogAccount::findOrFail($id);
        if (!$googleAccount) {
            return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
        }
        \Cache::set('google_dialog_account_id', $id);
        $googleDialogFlowService = new DialogFlowService($googleAccount);
        $authUrl = $googleDialogFlowService->getAuthorizationUrl();
        if ($authUrl) {
            return Redirect::away($authUrl);
        }
        return Redirect::route('google-chatbot-accounts')->with('error', 'Something went wrong');
    }

    /**
     * Connect google account
     * @param Request $request
     * @return RedirectResponse
     */
    public function googleLogin(Request $request): RedirectResponse
    {
        try {
            $id = \Cache::get('google_dialog_account_id');
            $googleAccount = GoogleDialogAccount::findOrFail($id);
            if (!$googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleDialogFlowService = new DialogFlowService($googleAccount);
            $googleDialogFlowService->getAccessToken($request->all());
            return Redirect::route('google-chatbot-accounts')->with('success', 'Account connected successfully!');
        } catch (\Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Disconnect google account
     * @param Request $request
     * @return RedirectResponse
     */
    public function disconnectAccount(Request $request, $id): RedirectResponse
    {
        try {
            $googleAccount = GoogleDialogAccountMails::findOrFail($id);
            if (!$googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleAccount->delete();
            return Redirect::route('google-chatbot-accounts')->with('success', 'Account disconnected successfully!');
        } catch (\Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }
}
