<?php

namespace App\Http\Controllers\Pinterest;

use App\Http\Controllers\Controller;
use App\PinterestAdsAccounts;
use App\PinterestCampaigns;
use App\PinterestPins;
use Illuminate\Http\Request;
use App\PinterestBoards;
use App\PinterestBoardSections;
use App\PinterestBusinessAccountMails;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Validator;

class PinterestCampaignsController extends Controller
{

    /**
     * Get all campaigns for a account.
     * @param Request $request
     * @param $id
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function campaignsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (!$pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestCampaigns = PinterestCampaigns::with(['account'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%' . $request->name . '%');
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'pins');
            $pinterestAdsAccounts = PinterestAdsAccounts::where('pinterest_mail_id', $pinterestBusinessAccountMail->id)->pluck('ads_account_name', 'id')->toArray();
            return view('pinterest.campaigns-index', compact('pinterestBusinessAccountMail', 'pinterestCampaigns', 'pinterestAdsAccounts'));
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Campaign.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function createCampaign(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (!$pinterestAccount) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_ads_account_id' => 'required',
                'name' => 'required',
                'status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'lifetime_spend_cap' => 'required_without:daily_spend_cap|integer|nullable',
                'daily_spend_cap' => 'required_without:lifetime_spend_cap|integer|nullable',
//                'tracking_urls_impression' => 'sometimes|array|max:3',
//                'tracking_urls_impression.*' => 'sometimes|url|nullable|max:2000',
//                'tracking_urls_click' => 'sometimes|array|max:3',
//                'tracking_urls_click.*' => 'sometimes|url|nullable|max:2000',
//                'tracking_urls_engagement' => 'sometimes|array|max:3',
//                'tracking_urls_engagement.*' => 'sometimes|url|nullable|max:2000',
//                'tracking_urls_buyable_button' => 'sometimes|array|max:3',
//                'tracking_urls_buyable_button.*' => 'sometimes|url|nullable|max:2000',
//                'tracking_urls_audience_verification' => 'sometimes|array|max:3',
//                'tracking_urls_audience_verification.*' => 'sometimes|url|nullable|max:2000',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'summary_status' => 'sometimes|nullable|in:RUNNING,PAUSED,NOT_STARTED,COMPLETED,ADVERTISER_DISABLED,ARCHIVED',
                'is_flexible_daily_budgets' => '',
                'objective_type' => 'required|in:AWARENESS,CONSIDERATION,VIDEO_VIEW,WEB_CONVERSION,CATALOG_SALES'
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdsAccount = PinterestAdsAccounts::where('id', $request->get('pinterest_ads_account_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $urls = $this->buildTrackingUrls($request->all());
            $response = $pinterest->createCampaign($pinterestAdsAccount->ads_account_id, [
                [
                    'ad_account_id' => $pinterestAdsAccount->ads_account_id,
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('lifetime_spend_cap') ? $request->get('lifetime_spend_cap', 0) * 1000000 : null,
                    'daily_spend_cap' => $request->has('daily_spend_cap') ? $request->get('daily_spend_cap', 0) * 1000000 : null,
//                    'tracking_urls' => count($urls) > 0 ? $this->buildTrackingUrls($request->all()) : null,
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'summary_status' => $request->get('summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('is_flexible_daily_budgets') ? $request->get('is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false,
                    'objective_type' => $request->get('objective_type'),
                ]
            ]);
            if ($response['status']) {
                PinterestCampaigns::create([
                    'pinterest_ads_account_id' => $pinterestAdsAccount->id,
                    'campaign_id' => $response['data']['items'][0]['data']['id'],
                    'name' => $request->get('name'),
                    'status' => $request->get('status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('lifetime_spend_cap') ? $request->get('lifetime_spend_cap', 0) : null,
                    'daily_spend_cap' => $request->has('daily_spend_cap') ? $request->get('daily_spend_cap', 0) : null,
//                    'tracking_urls' => json_encode($this->buildTrackingUrls($request->all())),
                    'start_time' => $request->has('start_time') && $request->start_time ? strtotime($request->get('start_time')) : null,
                    'end_time' => $request->has('end_time') && $request->end_time ? strtotime($request->get('end_time')) : null,
                    'summary_status' => $request->get('summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('is_flexible_daily_budgets') ? $request->get('is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false,
                    'objective_type' => $request->get('objective_type'),
                ]);
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('success', 'Campaign created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts.campaign.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Build Tracking URL object
     * @param $params
     * @return array
     */
    public function buildTrackingUrls($params, $isEdit = false): array
    {
        $urls = [
            'impression' => [],
            'click' => [],
            'engagement' => [],
            'buyable_button' => [],
            'audience_verification' => [],
        ];
        if (isset($params[$isEdit ? 'edit_tracking_urls_impression' : 'tracking_urls_impression'])) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_impression' : 'tracking_urls_impression']);
            if (count($values) > 0) {
                $urls['impression'] = $values;
            } else {
                unset($urls['impression']);
            }
        } else {
            unset($urls['impression']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']) && count($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_click' : 'tracking_urls_click']);
            if (count($values) > 0) {
                $urls['click'] = $values;
            } else {
                unset($urls['click']);
            }
        } else {
            unset($urls['click']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']) && count($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_engagement' : 'tracking_urls_engagement']);
            if (count($values) > 0) {
                $urls['engagement'] = $values;
            } else {
                unset($urls['engagement']);
            }
        } else {
            unset($urls['engagement']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']) && count($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_buyable_button' : 'tracking_urls_buyable_button']);
            if (count($values) > 0) {
                $urls['buyable_button'] = $values;
            } else {
                unset($urls['buyable_button']);
            }
        } else {
            unset($urls['buyable_button']);
        }
        if (isset($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']) && count($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']) > 0) {
            $values = array_filter($params[$isEdit ? 'edit_tracking_urls_audience_verification' : 'tracking_urls_audience_verification']);
            if (count($values) > 0) {
                $urls['audience_verification'] = $values;
            } else {
                unset($urls['audience_verification']);
            }
        } else {
            unset($urls['audience_verification']);
        }
        return $urls;
    }

    /**
     * Get Campaign Details
     * @param $id
     * @param $campaignId
     * @return JsonResponse
     */
    public function getCampaign($id, $campaignId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (!$pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestCampaign = PinterestCampaigns::findOrFail($campaignId);
            if (!$pinterestCampaign) {
                return response()->json(['status' => false, 'message' => 'Campaign not found']);
            }
            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestCampaign->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Campaign.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateCampaign(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (!$pinterestAccount) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_campaign_id' => 'required',
                'edit_pinterest_ads_account_id' => 'required',
                'edit_name' => 'required',
                'edit_status' => 'required|in:ACTIVE,PAUSED,ARCHIVED',
                'edit_lifetime_spend_cap' => 'required_without:edit_daily_spend_cap|integer|nullable',
                'edit_daily_spend_cap' => 'required_without:edit_lifetime_spend_cap|integer|nullable',
//                'edit_tracking_urls_impression' => 'sometimes|array|max:3',
//                'edit_tracking_urls_impression.*' => 'sometimes|url|nullable|max:2000',
//                'edit_tracking_urls_click' => 'sometimes|array|max:3',
//                'edit_tracking_urls_click.*' => 'sometimes|url|nullable|max:2000',
//                'edit_tracking_urls_engagement' => 'sometimes|array|max:3',
//                'edit_tracking_urls_engagement.*' => 'sometimes|url|nullable|max:2000',
//                'edit_tracking_urls_buyable_button' => 'sometimes|array|max:3',
//                'edit_tracking_urls_buyable_button.*' => 'sometimes|url|nullable|max:2000',
//                'edit_tracking_urls_audience_verification' => 'sometimes|array|max:3',
//                'edit_tracking_urls_audience_verification.*' => 'sometimes|url|nullable|max:2000',
                'edit_start_time' => 'required|date',
                'edit_end_time' => 'required|date',
                'edit_summary_status' => 'sometimes|nullable|in:RUNNING,PAUSED,NOT_STARTED,COMPLETED,ADVERTISER_DISABLED,ARCHIVED',
                'edit_is_flexible_daily_budgets' => ''
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestCampaign = PinterestCampaigns::where('id', $request->get('edit_campaign_id'))->first();
            $pinterestAdsAccount = PinterestAdsAccounts::where('id', $request->get('edit_pinterest_ads_account_id'))->first();
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $urls = $this->buildTrackingUrls($request->all(), true);
            $response = $pinterest->updateCampaign($pinterestAdsAccount->ads_account_id, [
                [
                    'id' => $pinterestCampaign->campaign_id,
                    'ad_account_id' => $pinterestAdsAccount->ads_account_id,
                    'name' => $request->get('edit_name'),
                    'status' => $request->get('edit_status', 'ACTIVE'),
                    'lifetime_spend_cap' => $request->has('edit_lifetime_spend_cap') && $request->edit_lifetime_spend_cap ? $request->get('edit_lifetime_spend_cap', 0) * 1000000 : null,
                    'daily_spend_cap' => $request->has('edit_daily_spend_cap') && $request->edit_daily_spend_cap ? $request->get('edit_daily_spend_cap', 0) * 1000000 : null,
//                    'tracking_urls' => count($urls) > 0 ? $urls : null,
                    'start_time' => $request->has('edit_start_time') && $request->edit_start_time ? strtotime($request->get('edit_start_time')) * 1000 : null,
                    'end_time' => $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) * 1000 : null,
                    'summary_status' => $request->get('edit_summary_status'),
                    'is_campaign_budget_optimization' => false,
                    'is_flexible_daily_budgets' => $request->has('edit_is_flexible_daily_budgets') ? $request->get('edit_is_flexible_daily_budgets') == 'true' : false,
                    'default_ad_group_budget_in_micro_currency' => null,
                    'is_automated_campaign' => false
                ]
            ]);
            if ($response['status']) {
                $pinterestCampaign->pinterest_ads_account_id = $pinterestAdsAccount->id;
                $pinterestCampaign->name = $request->get('edit_name');
                $pinterestCampaign->status = $request->get('edit_status', 'ACTIVE');
                $pinterestCampaign->lifetime_spend_cap = $request->has('edit_lifetime_spend_cap') && $request->edit_lifetime_spend_cap ? $request->get('edit_lifetime_spend_cap', 0) : null;
                $pinterestCampaign->daily_spend_cap = $request->has('edit_daily_spend_cap') && $request->edit_daily_spend_cap ? $request->get('edit_daily_spend_cap', 0) : null;
//                $pinterestCampaign->tracking_urls = json_encode($this->buildTrackingUrls($request->all(), true));
                $pinterestCampaign->start_time = $request->has('edit_start_time') && $request->edit_start_time ? strtotime($request->get('edit_start_time')) * 1000 : null;
                $pinterestCampaign->end_time = $request->has('edit_end_time') && $request->edit_end_time ? strtotime($request->get('edit_end_time')) * 1000 : null;
                $pinterestCampaign->summary_status = $request->get('edit_summary_status');
                $pinterestCampaign->is_campaign_budget_optimization = false;
                $pinterestCampaign->is_flexible_daily_budgets = $request->has('edit_is_flexible_daily_budgets') ? $request->get('edit_is_flexible_daily_budgets') == 'true' : false;
                $pinterestCampaign->default_ad_group_budget_in_micro_currency = null;
                $pinterestCampaign->is_automated_campaign = false;
                $pinterestCampaign->save();
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('success', 'Campaign updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.campaign.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (\Exception $e) {
            return Redirect::route('pinterest.accounts.campaign.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Pinterest Client
     * @param $pinterestAccount
     * @return PinterestService
     * @throws \Exception
     */
    public function getPinterestClient($pinterestAccount): PinterestService
    {
        $pinterest = new PinterestService($pinterestAccount->account->pinterest_client_id, $pinterestAccount->account->pinterest_client_secret, $pinterestAccount->account->id);
        $pinterest->updateAccessToken($pinterestAccount->pinterest_access_token);
        return $pinterest;
    }

}
