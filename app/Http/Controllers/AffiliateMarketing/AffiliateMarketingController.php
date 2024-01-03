<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\Setting;
use App\StoreWebsite;
use App\AffiliateProviders;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\AffiliateProviderAccounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

/**
 * Affiliate Marketing controller to manage multiple affiliate providers
 */
class AffiliateMarketingController extends Controller
{
    /**
     * Gets all the affiliate providers accounts
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function providerAccounts(Request $request)
    {
        $providers = AffiliateProviders::where('status', 1)->get();
        $storeWebsites = StoreWebsite::get();
        $providerAccounts = AffiliateProviderAccounts::where(function ($query) use ($request) {
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status == 'active');
            }
            if ($request->has('site') && $request->site) {
                $query->whereHas('storeWebsite', function ($query) use ($request) {
                    $query->orWhere(function ($query) use ($request) {
                        $query->where('website', 'like', '%' . $request->site . '%');
                        $query->where('title', 'like', '%' . $request->site . '%');
                    });
                });
            }
            if ($request->has('provider_name') && $request->provider_name) {
                $query->whereHas('provider', function ($query) use ($request) {
                    $query->where('provider_name', 'like', '%' . $request->provider_name . '%');
                });
            }
        })->with(['provider', 'storeWebsite'])->paginate(Setting::get('pagination'), ['*'], 'accounts_per_page');

        return view('affiliate-marketing.sites', compact('providers', 'storeWebsites', 'providerAccounts'));
    }

    /**
     * Inserts the providers account into the database.
     */
    public function createProviderAccount(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_website_id' => 'required',
                'affiliates_provider_id' => 'required',
                'api_key' => 'required',
                'status' => 'required|in:true,false',
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerAccounts')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            AffiliateProviderAccounts::create([
                'api_key' => $request->api_key,
                'store_website_id' => $request->store_website_id,
                'affiliates_provider_id' => $request->affiliates_provider_id,
                'status' => $request->status == 'true',
            ]);

            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('success', 'Provider account added successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Updates the providers account into database.
     */
    public function updateProviderAccounts(Request $request, $id): RedirectResponse
    {
        try {
            $provider = AffiliateProviderAccounts::findOrFail($id);
            if (! $provider) {
                return Redirect::route('affiliate-marketing.providerAccounts')
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'store_website_id' => 'required',
                'affiliates_provider_id' => 'required',
                'api_key' => 'required',
                'status' => 'required|in:true,false',
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerAccounts')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider->affiliates_provider_id = $request->affiliates_provider_id;
            $provider->store_website_id = $request->store_website_id;
            $provider->api_key = $request->api_key;
            $provider->status = $request->status == 'true';
            $provider->save();

            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('success', 'Provider account updated successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get the provider account by id
     */
    public function getProviderAccount(Request $request, $id): JsonResponse
    {
        try {
            $provider = AffiliateProviderAccounts::findOrFail($id);
            if (! $provider) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $provider->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a provider account by id
     */
    public function deleteProviderAccount(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerAccounts')
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider = AffiliateProviderAccounts::findOrFail($request->id);
            if (! $provider) {
                return Redirect::route('affiliate-marketing.providerAccounts')
                    ->with('error', 'No account found');
            }
            $provider->delete();

            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('success', 'Provider account removed successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providerAccounts')
                ->with('error', $e->getMessage());
        }
    }
}
