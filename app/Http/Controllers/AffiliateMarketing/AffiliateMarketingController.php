<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateProviders;
use App\AffiliateProviderSites;
use App\Http\Controllers\Controller;
use App\Setting;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

/**
 * Affiliate Marketing controller to manage multiple affiliate providers
 */
class AffiliateMarketingController extends Controller {

    /**
     * Gets all the affiliate marketing providers
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function providers(Request $request) {
        $providers = AffiliateProviders::where(function ($query) use ($request) {
            if ($request->has('provider_name') && $request->provider_name) {
                $query->where('provider_name', 'like', '%' . $request->provider_name . '%');
            }
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status == 'active');
            }
        })->paginate(Setting::get('pagination'), ['*'], 'providers_per_page');
        return view('affiliate-marketing.index', compact('providers'));
    }

    /**
     * Inserts the providers into the database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createProvider(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'provider_name' => 'required',
                'status' => 'required|in:true,false'
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providers')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            AffiliateProviders::create([
                'provider_name' => $request->provider_name,
                'status' => $request->status == 'true'
            ]);
            return Redirect::route('affiliate-marketing.providers')
                ->with('success', 'Provider added successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providers')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Updates the providers into database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateProvider(Request $request, $id): RedirectResponse {
        try {
            $provider = AffiliateProviders::findOrFail($id);
            if (!$provider) {
                return Redirect::route('affiliate-marketing.providers')
                    ->with('error', 'No provider found');
            }
            $validator = Validator::make($request->all(), [
                'provider_name' => 'required',
                'status' => 'required|in:true,false'
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providers')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider->provider_name = $request->provider_name;
            $provider->status = $request->status == 'true';
            $provider->save();
            return Redirect::route('affiliate-marketing.providers')
                ->with('success', 'Provider updated successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providers')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get the provider by id
     *
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function getProvider(Request $request, $id): JsonResponse {
        try {
            $provider = AffiliateProviders::findOrFail($id);
            if (!$provider) {
                return response()->json(['status' => false, 'message' => 'Provider not found']);
            }
            return response()->json(['status' => true, 'message' => 'Provider found', 'data' => $provider->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a provider by id
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteProvider(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providers')
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider = AffiliateProviders::findOrFail($request->id);
            if (!$provider) {
                return Redirect::route('affiliate-marketing.providers')
                    ->with('error', 'No provider found');
            }
            $provider->delete();
            return Redirect::route('affiliate-marketing.providers')
                ->with('success', 'Provider deleted successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providers')
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Gets all the affiliate marketing providers
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function providerSites(Request $request) {
        ini_set('memory_limit', '-1');
        $providers = AffiliateProviders::where('status', 1)->get();
        $storeWebsites = StoreWebsite::get();
        $providerSites = AffiliateProviderSites::where(function ($query) use ($request) {
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status == 'active');
            }
        })->with(['provider','storeWebsite'])
            ->paginate(Setting::get('pagination'), ['*'], 'providers_per_page');
//        echo '<pre>';dd($providerSites);
            return view('affiliate-marketing.sites', compact('providers', 'storeWebsites', 'providerSites'));
    }

    /**
     * Inserts the providers into the database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createProviderSite(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'store_website_id' => 'required',
                'affiliates_provider_id' => 'required',
                'api_key' => 'required',
                'status' => 'required|in:true,false'
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerSites')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            AffiliateProviderSites::create([
                'api_key' => $request->api_key,
                'store_website_id' => $request->store_website_id,
                'affiliates_provider_id' => $request->affiliates_provider_id,
                'status' => $request->status == 'true'
            ]);
            return Redirect::route('affiliate-marketing.providerSites')
                ->with('success', 'Site is added successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providers')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Updates the providers into database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateProviderSite(Request $request, $id): RedirectResponse {
        try {
            $provider = AffiliateProviderSites::findOrFail($id);
            if (!$provider) {
                return Redirect::route('affiliate-marketing.providerSites')
                    ->with('error', 'No site found');
            }
            $validator = Validator::make($request->all(), [
                'store_website_id' => 'required',
                'affiliates_provider_id' => 'required',
                'api_key' => 'required',
                'status' => 'required|in:true,false'
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerSites')
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider->affiliates_provider_id = $request->affiliates_provider_id;
            $provider->store_website_id = $request->store_website_id;
            $provider->api_key = $request->api_key;
            $provider->status = $request->status == 'true';
            $provider->save();
            return Redirect::route('affiliate-marketing.providerSites')
                ->with('success', 'Site updated successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providerSites')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get the provider by id
     *
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function getProviderSite(Request $request, $id): JsonResponse {
        try {
            $provider = AffiliateProviderSites::findOrFail($id);
            if (!$provider) {
                return response()->json(['status' => false, 'message' => 'Site not found']);
            }
            return response()->json(['status' => true, 'message' => 'Site found', 'data' => $provider->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a provider by id
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteProviderSite(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return Redirect::route('affiliate-marketing.providerSites')
                    ->withErrors($validator)
                    ->withInput();
            }
            $provider = AffiliateProviderSites::findOrFail($request->id);
            if (!$provider) {
                return Redirect::route('affiliate-marketing.providerSites')
                    ->with('error', 'No site found');
            }
            $provider->delete();
            return Redirect::route('affiliate-marketing.providerSites')
                ->with('success', 'Site removed successfully');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.providerSites')
                ->with('error', $e->getMessage());
        }
    }
}
