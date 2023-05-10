<?php

namespace App\Http\Controllers\Pinterest;

use App\Http\Controllers\Controller;
use App\PinterestAdsAccounts;
use App\PinterestBusinessAccountMails;
use App\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Validator;

class PinterestAdsAccountsController extends Controller
{
    public function __construct()
    {
        View::share([
            'countries' => (new PinterestService())->getSupportedCountries()
        ]);
    }

    /**
     * Show list of ads account for the connected account.
     * @param Request $request
     * @param $id
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function dashboard(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (!$pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterestAdsAccounts = PinterestAdsAccounts::where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                if ($request->has('name') && $request->name) {
                    $query->where('ads_account_name', 'like', '%' . $request->name . '%');
                }
                if ($request->has('country') && $request->country) {
                    $query->where('ads_account_country', 'like', '%' . $request->country . '%');
                }
                if ($request->has('currency') && $request->currency) {
                    $query->where('ads_account_currency', 'like', '%' . $request->currency . '%');
                }
            })->paginate(Setting::get('pagination'), ['*'], 'ads_accounts');
            return view('pinterest.account-dashboard', compact('pinterestBusinessAccountMail', 'pinterestAdsAccounts'));
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Ads account.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function createAdsAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (!$pinterestAccount) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'country' => 'required'
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterest = new PinterestService($pinterestAccount->account->pinterest_client_id, $pinterestAccount->account->pinterest_client_secret, $pinterestAccount->account->id);
            $pinterest->updateAccessToken($pinterestAccount->pinterest_access_token);
            $response = $pinterest->createAdsAccount([
                "name" => $request->get('name'),
                "country" => $request->get('country')
            ]);
            if ($response['status']) {
                PinterestAdsAccounts::create([
                    'pinterest_mail_id' => $pinterestAccount->id,
                    'ads_account_id' => $response['data']['id'],
                    'ads_account_name' => $request->get('name'),
                    'ads_account_country' => $request->get('country'),
                    'ads_account_currency' => $response['data']['currency']
                ]);
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('success', 'Ads account created.');
            } else {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', $response['message']);
            }
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }
}
