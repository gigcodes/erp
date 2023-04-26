<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateCommissions;
use App\AffiliateConversions;
use App\AffiliateGroups;
use App\AffiliateMarketers;
use App\AffiliateMarketingLogs;
use App\AffiliatePayments;
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
                $this->logActivity('Sync commissions Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync commissions Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync commissions Data', ['status' => false, 'message' => $e->getMessage()]);
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
     * Get List of all affiliates in a provider account
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function affiliateIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersAffiliates = AffiliateMarketers::with('group')->where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('name') && $request->name) {
                    $query->orWhere(function ($query) use ($request) {
                        $query->where('firstName', 'like', '%' . $request->firstName . '%');
                        $query->where('lastName', 'like', '%' . $request->lastName . '%');
                        $query->where('email', 'like', '%' . $request->email . '%');
                    });
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');
            $affiliateGroups = AffiliateGroups::where('affiliate_account_id', $provider->id)->get();
            $affiliateProgrammes = AffiliatePrograms::where('affiliate_account_id', $provider->id)->get();
            return view('affiliate-marketing.providers.affiliates', compact('providersAffiliates', 'provider', 'affiliateGroups', 'affiliateProgrammes'));
        }
        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', 'No provider found');
    }

    /**
     * Sync Affiliates from API to DB
     * @param Request $request
     * @return RedirectResponse
     */
    public function affiliateSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAffiliates();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $affiliate) {
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $affiliate['id'])->first();
                        $groupData = AffiliateGroups::where('affiliate_provider_group_id', $affiliate['affiliate_group_id'])->first();
                        if (!$affiliateData) {
                            AffiliateMarketers::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_id' => $affiliate['id'],
                                'firstname' => $affiliate['firstname'],
                                'lastname' => $affiliate['lastname'],
                                'email' => $affiliate['email'],
                                'company_name' => $affiliate['company'] && $affiliate['company']['name'],
                                'company_description' => $affiliate['company'] && $affiliate['company']['description'],
                                'address_one' => $affiliate['address'] && $affiliate['address']['address'],
                                'address_two' => $affiliate['address'] && $affiliate['address']['address_two'],
                                'address_postal_code' => $affiliate['address'] && $affiliate['address']['postal_code'],
                                'address_city' => $affiliate['address'] && $affiliate['address']['city'],
                                'address_state' => $affiliate['address'] && $affiliate['address']['state'],
                                'address_country_code' => $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['code'],
                                'address_country_name' => $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['name'],
                                'meta_data' => $affiliate['meta_data'] ? serialize($affiliate['meta_data']) : null,
                                'parent_id' => $affiliate['parent_id'],
                                'affiliate_created_at' => $affiliate['created_at'],
                                'affiliate_group_id' => $groupData->id,
                                'promoted_at' => $affiliate['promoted_at'],
                                'promotion_method' => $affiliate['promotion_method']
                            ]);
                        } else {
                            $affiliateData->affiliate_id = $affiliate['id'];
                            $affiliateData->firstname = $affiliate['firstname'];
                            $affiliateData->lastname = $affiliate['lastname'];
                            $affiliateData->email = $affiliate['email'];
                            $affiliateData->company_name = $affiliate['company'] && $affiliate['company']['name'];
                            $affiliateData->company_description = $affiliate['company'] && $affiliate['company']['description'];
                            $affiliateData->address_one = $affiliate['address'] && $affiliate['address']['address'];
                            $affiliateData->address_two = $affiliate['address'] && $affiliate['address']['address_two'];
                            $affiliateData->address_postal_code = $affiliate['address'] && $affiliate['address']['postal_code'];
                            $affiliateData->address_city = $affiliate['address'] && $affiliate['address']['city'];
                            $affiliateData->address_state = $affiliate['address'] && $affiliate['address']['state'];
                            $affiliateData->address_country_code = $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['code'];
                            $affiliateData->address_country_name = $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['name'];
                            $affiliateData->meta_data = $affiliate['meta_data'] ? serialize($affiliate['meta_data']) : null;
                            $affiliateData->parent_id = $affiliate['parent_id'];
                            $affiliateData->affiliate_created_at = $affiliate['created_at'];
                            $affiliateData->affiliate_group_id = $groupData->id;
                            $affiliateData->promoted_at = $affiliate['promoted_at'];
                            $affiliateData->promotion_method = $affiliate['promotion_method'];
                            $affiliateData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate Data', $responseData);
                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate synced successfully');
                }
                $this->logActivity('Sync affiliate Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync affiliate Data', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create new affiliate on provider and save in the database
     * @param Request $request
     * @return RedirectResponse
     */
    public function affiliateCreate(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'sometimes|email',
                'affiliate_group_id' => 'required',
            ]);
            if ($validator->fails()) {
                $this->logActivity('Create Affiliate', ['status' => false, 'message' => $validator->errors()->first()]);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliate([
                    'firstname' => $request->firstName, 'lastname' => $request->lastName, 'email' => $request->email,
                    'company' => ['name' => $request->company_name, 'description' => $request->company_description],
                    'address' => [
                        'address' => $request->address_one, 'address_two' => $request->address_two, 'postal_code' => $request->address_postal_code,
                        'city' => $request->address_city, 'state' => $request->address_state, 'country' => [
                            'code' => $request->address_country_code, 'name' => $request->address_country_name
                        ]
                    ],
                ]);
                if ($responseData['status']) {
                    $groupData = AffiliateGroups::where('id', $request->affiliate_group_id)->first();
                    $tapfiliate->setAffiliateGroupForAffiliate($responseData['data']['id'], ['group_id' => $groupData->affiliate_provider_group_id]);
                    AffiliateMarketers::create([
                        'affiliate_account_id' => $request->affiliate_account_id,
                        'affiliate_id' => $responseData['data']['id'],
                        'firstname' => $request->firstName,
                        'lastname' => $request->lastName,
                        'email' => $request->email,
                        'company_name' => $request->company_name,
                        'company_description' => $request->company_description,
                        'address_one' => $request->address_one,
                        'address_two' => $request->address_two,
                        'address_postal_code' => $request->address_postal_code,
                        'address_city' => $request->address_city,
                        'address_state' => $request->address_state,
                        'address_country_code' => $request->address_country_code,
                        'address_country_name' => $request->address_country_name,
                        'meta_data' => null,
                        'parent_id' => null,
                        'affiliate_created_at' => null,
                        'affiliate_group_id' => $groupData->id,
                        'promoted_at' => null,
                        'promotion_method' => null,
                    ]);
                    $this->logActivity('Create Affiliate', $responseData);
                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate added successfully');
                }
                $this->logActivity('Create Affiliate', $responseData);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Create Affiliate', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete affiliate on provider also in DB
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function affiliateDelete(Request $request, $id): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (!$affiliate) {
                $this->logActivity('Delete Affiliate', ['status' => false, 'message' => "Affiliate not found"]);
                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->deleteAffiliate($affiliate->affiliate_id);
                if ($responseData['status']) {
                    $affiliate->delete();
                    $this->logActivity('Delete Affiliate', $responseData);
                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate Deleted successfully');
                }
                $this->logActivity('Delete Affiliate', $responseData);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Affiliate', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', 'Account Not found');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all payout methods for affiliates
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function affiliatePayoutMethods(Request $request, $id): JsonResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (!$affiliate) {
                $response = ['status' => false, 'message' => 'Affiliate not found'];
                $this->logActivity('Get All Affiliate payout methods', $response);
                return response()->json($response);
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAllAffiliatePayoutMethods($affiliate->affiliate_id);
                if ($responseData['status']) {
                    $response = ['status' => true, 'message' => 'Affiliate payout methods found', 'data' => $responseData['data']];
                    $this->logActivity('Get Affiliate payout methods', $response);
                    return response()->json($response);
                }
                $response = ['status' => false, 'message' => 'Affiliate payout methods not found'];
                $this->logActivity('Get Affiliate payout methods', $response);
                return response()->json($response);
            }
            $response = ['status' => false, 'message' => 'Account not found'];
            $this->logActivity('Get Affiliate payout methods', $response);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate payout methods', $response);
            return response()->json($response);
        }
    }

    /**
     * Update the payout method for affiliate
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function affiliateUpdatePayoutMethod(Request $request, $id): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (!$affiliate) {
                $this->logActivity('Update Affiliate Payout method', ['status' => false, 'message' => "Affiliate not found"]);
                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->setAffiliatePayoutMethods($affiliate->affiliate_id, $request->payout_id);
                if ($responseData['status']) {
                    $affiliate->payout = $request->payout_id;
                    $affiliate->save();
                    $this->logActivity('Update Affiliate Payout method', $responseData);
                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payout method updated successfully');
                }
                $this->logActivity('Update Affiliate Payout method', $responseData);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Payout method', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', 'Account Not found');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate programme
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function affiliateAddToProgramme(Request $request): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($request->affiliate_id);
            if (!$affiliate) {
                $this->logActivity('Update Affiliate Programme method', ['status' => false, 'message' => "Affiliate not found"]);
                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $programme = AffiliatePrograms::find($request->programme_id);
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->addAffiliateToProgramme($programme->affiliate_program_id, [
                    'affiliate' => ['id' => $affiliate->affiliate_id],
                    'approved' => $request->has('approved') && $request->approved ? ($request->approved == 'true') : null,
                    'coupon' => $request->coupon
                ]);
                if ($responseData['status']) {
                    $affiliate->referral_link = $responseData['data']['referral_link']['link'];
                    $affiliate->asset_id = $responseData['data']['referral_link']['asset_id'];
                    $affiliate->source_id = $responseData['data']['referral_link']['source_id'];
                    $affiliate->approved = $request->approved == 'true';
                    $affiliate->coupon = $request->coupon;
                    $affiliate->affiliate_programme_id = $programme->id;
                    $affiliate->save();
                    $this->logActivity('Update Affiliate programme method', $responseData);
                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payout method updated successfully');
                }
                $this->logActivity('Update Affiliate programme method', $responseData);
                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate programme method', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', 'Account Not found');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function paymentsIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersPayments = AffiliatePayments::with('affiliate')->where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('name') && $request->name) {
                    $query->whereHas('affiliate', function ($query) use ($request) {
                        $query->where('firstName', 'like', '%' . $request->firstName . '%');
                        $query->where('lastName', 'like', '%' . $request->lastName . '%');
                        $query->where('email', 'like', '%' . $request->email . '%');
                    });
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_payments');
            $affiliates = AffiliateMarketers::where('affiliate_account_id', $provider->id)->get();
            return view('affiliate-marketing.providers.payments', compact('providersPayments', 'provider', 'affiliates'));
        }
        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', 'No provider found');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentsCreate(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'affiliate_id' => 'required',
                'amount' => 'required',
                'currency' => 'required|max:3'
            ]);
            if ($validator->fails()) {
                $this->logActivity('Create Affiliate payment', ['status' => false, 'message' => $validator->errors()->first()]);
                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            $affiliateData = AffiliateMarketers::find($request->affiliate_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliatePayment([
                    'affiliate_id' => $affiliateData->affiliate_id,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                ]);
                if ($responseData['status']) {
                    AffiliatePayments::create([
                        'affiliate_account_id' => $request->provider_account,
                        'payment_id' => $responseData['data'][0]['id'],
                        'payment_created_at' => $responseData['data'][0]['created_at'],
                        'affiliate_marketer_id' => $affiliateData->id,
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                    ]);
                    $this->logActivity('Create Affiliate payment', $responseData);
                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payment added successfully');
                }
                $this->logActivity('Create Affiliate payment', $responseData);
                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate payment', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Create Affiliate payment', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentsCancel(Request $request, $id): RedirectResponse
    {
        try {
            $payment = AffiliatePayments::findOrFail($id);
            if (!$payment) {
                return Redirect::route('affiliate-marketing.provider.payments.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->cancelAffiliatePayment($payment->payment_id);
                if ($responseData['status']) {
                    $payment->delete();
                    $this->logActivity('Delete Affiliate payment', $responseData);
                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payment deleted successfully');
                }
                $this->logActivity('Delete Affiliate payment', $responseData);
                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Affiliate payment', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.payments.index')
                ->with('success', 'Account Not found');
        } catch (\Exception $e) {
            return Redirect::route('affiliate-marketing.provider.payments.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sync Payments from API to DB
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentsSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfilliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAffiliatePayment();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $payment) {
                        $paymentData = AffiliatePayments::where('payment_id', $payment['id'])->first();
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $payment['affiliate']['id'])->first();
                        if (!$paymentData) {
                            AffiliatePayments::create([
                                'affiliate_account_id' => $request->provider_account,
                                'payment_id' => $payment['id'],
                                'payment_created_at' => $payment['created_at'],
                                'affiliate_marketer_id' => $affiliateData->id,
                                'amount' => $payment['amount'],
                                'currency' => $payment['currency'],
                            ]);
                        } else {
                            $paymentData->payment_id = $payment['id'];
                            $paymentData->payment_created_at = $payment['created_at'];
                            $paymentData->affiliate_marketer_id = $affiliateData->id;
                            $paymentData->amount = $payment['amount'];
                            $paymentData->currency = $payment['currency'];
                            $paymentData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate Payment Data', $responseData);
                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate Payment synced successfully');
                }
                $this->logActivity('Sync affiliate Payment Data', $responseData);
                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate Payment Data', ['status' => false, 'message' => "Account Not found"]);
            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (\Exception $e) {
            $this->logActivity('Sync affiliate payments Data', ['status' => false, 'message' => $e->getMessage()]);
            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
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
