<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateGroups;
use App\AffiliateMarketingLogs;
use App\AffiliateProviderAccounts;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

/**
 * Affiliate Marketing data controller to manage multiple affiliate providers and accounts
 */
class AffiliateMarketingDataController extends Controller
{

    public function index(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersGroups = AffiliateGroups::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('group_name') && $request->group_name) {
                    $query->where('title', 'like', '%' . $request->group_name . '%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');
            return view('affiliate-marketing.providers.index', compact('providersGroups', 'provider'));
        }
        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', 'No provider found');
    }

    public function getAllAffiliates(Request $request)
    {
//        try {
//            $providerAccount = $this->getProviderAccount($request->provider_account);
//            $responseData = [];
//            if ($providerAccount->provider->provider_name === 'Tapfilliate') {
//                $tapfiliate = new Tapfiliate();
//                $responseData = $tapfiliate->getData($providerAccount);
//                _p($responseData);die;
//            }
//            _p($providerAccount);die;
//        } catch (\Exception $e) {
//
//        }
    }

    /**
     * Create new affiliate group on provider and save in the database
     * @param Request $request
     * @return RedirectResponse
     */
    public function createAffiliateGroup(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'affiliate_account_id' => 'required'
            ]);
            if ($validator->fails()) {
                $this->logActivity('Create Affiliate Group', ['status' => false, 'message' => $validator->errors()->first()]);
                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if ($providerAccount->provider->provider_name === 'Tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliateGroup(['title' => $request->title]);
                if ($responseData['status']) {
                    AffiliateGroups::create([
                        'title' => $request->title,
                        'affiliate_provider_group_id' => $responseData['data']['id'],
                        'affiliate_account_id' => $request->affiliate_account_id
                    ]);
                    $this->logActivity('Create Affiliate Group', $responseData);
                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group added successfully');
                }
                $this->logActivity('Create Affiliate Group', $responseData);
                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate Group', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Create Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate group on provider and save in the database
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAffiliateGroup(Request $request, $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'affiliate_account_id' => 'required'
            ]);
            if ($validator->fails()) {
                $this->logActivity('Update Affiliate Group', ['status' => false, 'message' => $validator->errors()->first()]);
                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->withErrors($validator)
                    ->withInput();
            }
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if ($providerAccount->provider->provider_name === 'Tapfilliate') {
                $groupData = AffiliateGroups::find($id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateGroup($groupData->affiliate_provider_group_id, ['title' => $request->title]);
                if ($responseData['status']) {
                    $groupData->title = $request->title;
                    $groupData->save();
                    $this->logActivity('Update Affiliate Group', $responseData);
                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group updated successfully');
                }
                $this->logActivity('Update Affiliate Group', $responseData);
                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Group', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Update Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get affiliate group details
     * @param Request $request
     * @return RedirectResponse
     */
    public function getAffiliateGroup(Request $request, $id): JsonResponse
    {
        try {
            $group = AffiliateGroups::findOrFail($id);
            if (!$group) {
                $response = ['status' => false, 'message' => 'Group not found'];
                $this->logActivity('Get Affiliate Group', $response);
                return response()->json($response);
            }
            $response = ['status' => true, 'message' => 'Group found', 'data' => $group->toArray()];
            $this->logActivity('Get Affiliate Group', $response);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate Group', $response);
            return response()->json($response);
        }
    }

    private function logActivity($name, $data)
    {
        AffiliateMarketingLogs::create([
            'user_name' => Auth::user()->name,
            'name' => $name,
            'status' => $data['status'] ? 'Success' : 'Error',
            'message' => $data['message'],
        ]);
    }

    private function getProviderAccount($providerId)
    {
        return AffiliateProviderAccounts::with('provider')->findOrFail($providerId);
    }
}
