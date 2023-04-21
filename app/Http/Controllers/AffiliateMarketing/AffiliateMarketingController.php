<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateProviders;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

/**
 * Affiliate Marketing controller to manage multiple affiliate providers
 */
class AffiliateMarketingController extends Controller
{

    /**
     * Gets all the affiliate marketing providers
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function providers(Request $request)
    {
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function createProvider(Request $request): RedirectResponse
    {
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateProvider(Request $request, $id): RedirectResponse
    {
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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getProvider(Request $request, $id): JsonResponse
    {
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteProvider(Request $request): RedirectResponse
    {
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
}
