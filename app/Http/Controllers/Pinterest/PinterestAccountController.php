<?php

namespace App\Http\Controllers\Pinterest;

use Validator;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\PinterestBusinessAccounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\PinterestBusinessAccountMails;
use Illuminate\Support\Facades\Redirect;

class PinterestAccountController extends Controller
{
    /**
     * Get all the pinterest account
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $pinterestBusinessAccounts = PinterestBusinessAccounts::with('accounts')->where(function ($query) use ($request) {
            if ($request->has('name') && $request->name) {
                $query->where('pinterest_application_name', 'like', '%' . $request->name . '%');
            }
            if ($request->has('is_active') && $request->is_active) {
                $query->where('is_active', $request->is_active === 'active');
            }
        })->paginate(Setting::get('pagination'), ['*'], 'pinterest-business');

        return view('pinterest.index', compact('pinterestBusinessAccounts'));
    }

    /**
     * Add pinterest account details in DB
     */
    public function createAccount(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'pinterest_application_name' => 'required',
                'pinterest_client_id' => 'required',
                'pinterest_client_secret' => 'required',
                'is_active' => 'required|in:true,false',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts')
                    ->with('create_popup', true)
                    ->withInput()
                    ->withErrors($validator);
            }
            PinterestBusinessAccounts::create([
                'pinterest_application_name' => $request->pinterest_application_name,
                'pinterest_client_id' => $request->pinterest_client_id,
                'pinterest_client_secret' => $request->pinterest_client_secret,
                'is_active' => $request->is_active === 'true',
            ]);

            return Redirect::route('pinterest.accounts')
                ->with('success', 'Account added successfully');
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Business account
     */
    public function getAccount(Request $request, $id): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccounts::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestBusinessAccount->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * update the business account
     */
    public function updateAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccounts::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_application_name' => 'required',
                'pinterest_client_id' => 'required',
                'pinterest_client_secret' => 'required',
                'is_active' => 'required|in:true,false',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestBusinessAccount->pinterest_application_name = $request->pinterest_application_name;
            $pinterestBusinessAccount->pinterest_client_id = $request->pinterest_client_id;
            $pinterestBusinessAccount->pinterest_client_secret = $request->pinterest_client_secret;
            $pinterestBusinessAccount->is_active = $request->is_active == 'true';
            $pinterestBusinessAccount->save();

            return Redirect::route('pinterest.accounts')
                ->with('success', 'Account updated successfully');
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete business account
     */
    public function deleteAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccounts::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            PinterestBusinessAccountMails::where('pinterest_business_account_id', $pinterestBusinessAccount->id)->delete();
            $pinterestBusinessAccount->delete();

            return Redirect::route('pinterest.accounts')
                ->with('success', 'Account deleted successfully');
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Generate authorize url and redirect it to get access token.
     */
    public function connectAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccounts::findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterest = new PinterestService($pinterestAccount->pinterest_client_id, $pinterestAccount->pinterest_client_secret, $pinterestAccount->id);
            $authUrl = $pinterest->getAuthURL();
            if ($authUrl) {
                return Redirect::away($authUrl);
            }

            return Redirect::route('pinterest.accounts')
                ->with('error', 'Unable to connect account');
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update access token and refresh token for connected account.
     */
    public function loginAccount(Request $request): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccounts::findOrFail(base64_decode($request->state));
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterest = new PinterestService($pinterestAccount->pinterest_client_id, $pinterestAccount->pinterest_client_secret, $pinterestAccount->id);
            $response = $pinterest->validateAccessTokenAndRefreshToken($request->all());
            if (! $response['status']) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', $response['message']);
            } else {
                $pinterestBusinessAccount = new PinterestBusinessAccountMails();
                $pinterestBusinessAccount->pinterest_business_account_id = $pinterestAccount->id;
                $pinterestBusinessAccount->pinterest_refresh_token = $response['data']['refresh_token'];
                $pinterestBusinessAccount->pinterest_access_token = $response['data']['access_token'];
                $pinterestBusinessAccount->expires_in = $response['data']['expires_in'];
                $pinterestBusinessAccount->refresh_token_expires_in = $response['data']['refresh_token_expires_in'];
                $pinterestBusinessAccount->save();
                $pinterest->updateAccessToken($pinterestBusinessAccount->pinterest_access_token);
                $userResponse = $pinterest->getUserAccount();
                if ($userResponse['status']) {
                    $pinterestBusinessAccount->pinterest_account = $userResponse['data']['username'];
                    $pinterestBusinessAccount->save();
                } else {
                    return Redirect::route('pinterest.accounts')
                        ->with('error', $userResponse['message']);
                }

                return Redirect::route('pinterest.accounts')
                    ->with('success', 'Account connected successfully');
            }
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Refresh the token and account details
     */
    public function refreshAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterest = new PinterestService($pinterestAccount->account->pinterest_client_id, $pinterestAccount->account->pinterest_client_secret, $pinterestAccount->account->id);
            $pinterest->updateAccessToken($pinterestAccount->pinterest_access_token);
            $response = $pinterest->getUserAccount();
            if ($response['status']) {
                $pinterestAccount->pinterest_account = $response['data']['username'];
                $pinterestAccount->save();

                return Redirect::route('pinterest.accounts')
                    ->with('success', 'Account refreshed successfully');
            } else {
                return Redirect::route('pinterest.accounts')
                    ->with('error', $response['message']);
            }
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Disconnect account from pinterest.
     */
    public function disconnectAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterestAccount->delete();

            return Redirect::route('pinterest.accounts')
                ->with('success', 'Account disconnected successfully');
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }
}
