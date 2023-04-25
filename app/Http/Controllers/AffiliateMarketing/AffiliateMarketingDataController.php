<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateCommissions;
use App\AffiliateConversions;
use App\AffiliateGroups;
use App\AffiliateMarketers;
use App\AffiliateMarketingLogs;
use App\AffiliatePrograms;
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

    /**
     * Get details for provider account
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
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
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
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
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
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

    public function syncData(Request $request)
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getSyncData();
                _p($responseData);
                die;
                if ($responseData['status']) {
                    $this->logActivity('Sync Affiliate Data', $responseData);
                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group updated successfully');
                }
                $this->logActivity('Sync Affiliate Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync Affiliate Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get List of all programmes in a provider account
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function programIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersProgrammes = AffiliatePrograms::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('title') && $request->title) {
                    $query->where('title', 'like', '%' . $request->group_name . '%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');
            return view('affiliate-marketing.providers.programes', compact('providersProgrammes', 'provider'));
        }
        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', 'No provider found');
    }

    /**
     * Sync Programmes from API to DB
     * @param Request $request
     * @return RedirectResponse
     */
    public function programSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getProgrammes();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $programme) {
                        $programmeData = AffiliatePrograms::where('affiliate_program_id', $programme['id'])->first();
                        if (!$programmeData) {
                            AffiliatePrograms::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_program_id' => $programme['id'],
                                'currency' => $programme['currency'],
                                'title' => $programme['title'],
                                'cookie_time' => $programme['cookie_time'],
                                'default_landing_page_url' => $programme['default_landing_page_url'],
                                'recurring' => (bool)$programme['recurring'],
                                'recurring_cap' => $programme['recurring_cap'],
                                'recurring_period_days' => $programme['recurring_period_days'],
                                'program_category_id' => $programme['program_category'] ? $programme['program_category']['id'] : null,
                                'program_category_identifier' => $programme['program_category'] ? $programme['program_category']['identifier'] : null,
                                'program_category_title' => $programme['program_category'] ? $programme['program_category']['title'] : null,
                                'program_category_is_admitad_suitable' => $programme['program_category'] && (bool)$programme['program_category']['is_admitad_suitable'],
                            ]);
                        } else {
                            $programmeData->currency = $programme['currency'];
                            $programmeData->title = $programme['title'];
                            $programmeData->cookie_time = $programme['cookie_time'];
                            $programmeData->default_landing_page_url = $programme['default_landing_page_url'];
                            $programmeData->recurring = (bool)$programme['recurring'];
                            $programmeData->recurring_cap = $programme['recurring_cap'];
                            $programmeData->recurring_period_days = $programme['recurring_period_days'];
                            $programmeData->program_category_id = $programme['program_category'] ? $programme['program_category']['id'] : null;
                            $programmeData->program_category_identifier = $programme['program_category'] ? $programme['program_category']['identifier'] : null;
                            $programmeData->program_category_title = $programme['program_category'] ? $programme['program_category']['title'] : null;
                            $programmeData->program_category_is_admitad_suitable = $programme['program_category'] && (bool)$programme['program_category']['is_admitad_suitable'];
                            $programmeData->save();
                        }
                    }
                    $this->logActivity('Sync Programme Data', $responseData);
                    return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate programmes synced successfully');
                }
                $this->logActivity('Sync Programme Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync Programme Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync Programme Group', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get List of all commission in a provider account
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function commissionIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersCommissions = AffiliateCommissions::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('amount') && $request->amount) {
                    $query->where('amount', $request->amount);
                }
                if ($request->has('commission_type') && $request->commission_type) {
                    $query->where('commission_type', 'like', '%' . $request->commission_type . '%');
                }
                if ($request->has('kind') && $request->kind) {
                    $query->where('kind', 'like', '%' . $request->kind . '%');
                }
                if ($request->has('currency') && $request->currency) {
                    $query->where('currency', 'like', '%' . $request->currency . '%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');
            return view('affiliate-marketing.providers.comissions', compact('providersCommissions', 'provider'));
        }
        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', 'No provider found');
    }

    /**
     * Sync Commissions from API to DB
     * @param Request $request
     * @return RedirectResponse
     */
    public function commissionSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getCommissions();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $commission) {
                        $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                        $affiliateMarketerData = null;
                        if ($commission['affiliate'] && $commission['affiliate']['id']) {
                            $affiliateMarketerData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                        }
                        if (!$commissionData) {
                            AffiliateCommissions::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_commission_id' => $commission['id'],
                                'amount' => $commission['amount'],
                                'approved' => (bool)$commission['approved'],
                                'affiliate_commission_created_at' => $commission['created_at'],
                                'commission_type' => $commission['commission_type'],
                                'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                'comment' => $commission['comment'],
                                'affiliate_conversion_id' => $commission['conversion'] && $commission['conversion']['id'],
                                'payout' => $commission['payout'],
                                'affiliate_marketer_id' => $affiliateMarketerData && $affiliateMarketerData->id,
                                'kind' => $commission['kind'],
                                'currency' => $commission['currency'],
                                'final' => $commission['final'],
                                'finalization_date' => $commission['finalization_date']
                            ]);
                        } else {
                            $commissionData->affiliate_commission_id = $commission['id'];
                            $commissionData->amount = $commission['amount'];
                            $commissionData->approved = (bool)$commission['approved'];
                            $commissionData->affiliate_commission_created_at = $commission['created_at'];
                            $commissionData->commission_type = $commission['commission_type'];
                            $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                            $commissionData->comment = $commission['comment'];
                            $commissionData->affiliate_conversion_id = $commission['conversion'] && $commission['conversion']['id'];
                            $commissionData->payout = $commission['payout'];
                            $commissionData->affiliate_marketer_id = $affiliateMarketerData && $affiliateMarketerData->id;
                            $commissionData->kind = $commission['kind'];
                            $commissionData->currency = $commission['currency'];
                            $commissionData->final = $commission['final'];
                            $commissionData->finalization_date = $commission['finalization_date'];
                            $commissionData->save();
                        }
                    }
                    $this->logActivity('Sync commissions Data', $responseData);
                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commissions synced successfully');
                }
                $this->logActivity('Sync Programme Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync commissions Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync commissions Group', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get affiliate commission details
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function commissionGet(Request $request, $id): JsonResponse
    {
        try {
            $commission = AffiliateCommissions::findOrFail($id);
            if (!$commission) {
                $response = ['status' => false, 'message' => 'Commission not found'];
                $this->logActivity('Get Affiliate Commission', $response);
                return response()->json($response);
            }
            $response = ['status' => true, 'message' => 'Affiliate Commission found', 'data' => $commission->toArray()];
            $this->logActivity('Get Affiliate Commission', $response);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate Commission', $response);
            return response()->json($response);
        }
    }

    /**
     * Update affiliate commission on provider and save in the database
     * @param Request $request
     * @return RedirectResponse
     */
    public function commissionUpdate(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required',
                'commission_id' => 'required',
            ]);
            if ($validator->fails()) {
                $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => $validator->errors()->first()]);
                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->withErrors($validator)
                    ->withInput();
            }
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $commissionData = AffiliateCommissions::find($request->commission_id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateCommission($commissionData->affiliate_commission_id, ['amount' => $request->amount]);
                if ($responseData['status']) {
                    $commissionData->amount = $request->amount;
                    $commissionData->save();
                    $this->logActivity('Update Affiliate Commission', $responseData);
                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commission updated successfully');
                }
                $this->logActivity('Update Affiliate commission', $responseData);
                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate commission on provider and save in the database
     * @param Request $request
     * @return RedirectResponse
     */
    public function commissionApproveDisapprove(Request $request, $id): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $commissionData = AffiliateCommissions::find($id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateApproveDisapprove($commissionData->affiliate_commission_id, !$commissionData->approved);
                if ($responseData['status']) {
                    $commissionData->approved = !$commissionData->approved;
                    $commissionData->save();
                    $this->logActivity('Update Affiliate Commission', $responseData);
                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commission updated successfully');
                }
                $this->logActivity('Update Affiliate commission', $responseData);
                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Logs activity for affiliate marketing
     * @param $name
     * @param $data
     */
    private function logActivity($name, $data)
    {
        AffiliateMarketingLogs::create([
            'user_name' => Auth::user()->name,
            'name' => $name,
            'status' => $data['status'] ? 'Success' : 'Error',
            'message' => $data['message'],
        ]);
    }

    /**
     * Gets provider account details.
     * @param $providerId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    private function getProviderAccount($providerId)
    {
        return AffiliateProviderAccounts::with('provider')->findOrFail($providerId);
    }
}
