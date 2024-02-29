<?php

namespace App\Http\Controllers;

use App\SiteAudit;
use Carbon\Carbon;
use App\Competitor;
use App\BacklinkAnchors;
use App\BacklinkDomains;
use App\DomainLandingPage;
use App\DomainOrganicPage;
use App\BacklinkIndexedPage;
use App\DomainSearchKeyword;
use Illuminate\Http\Request;

class DetailsController extends Controller
{
    public function compitetorsDetails($id, $type = 'organic')
    {
        $keywords = Competitor::where('store_website_id', $id)->where('subtype', $type)->get();
        if (request()->ajax()) {
            return view('seo-tools.partials.compitetors-data', compact('keywords'));
        }

        return view('seo-tools.compitetorsrecords', compact('keywords', 'id'));
    }

    public function domainDetails($id, $type = 'organic', $viewId = '', $viewTypeName = '')
    {
        $now               = Carbon::now()->format('Y-m-d');
        $keywords          = DomainSearchKeyword::where('store_website_id', $id)->where('subtype', $type)->where('created_at', 'like', $now . '%')->get();
        $domainorganicpage = DomainOrganicPage::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->get();
        $domainlandingpage = DomainLandingPage::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->get();
        $compitetors       = Competitor::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->get();
        if (request()->ajax()) {
            return view('seo-tools.partials.domain-data', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'viewId', 'viewTypeName'));
        }

        return view('seo-tools.records', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'id', 'viewId', 'viewTypeName'));
    }

    /**
     * This function is use to search Domain Details
     *
     * @param int    $id
     * @param string $type
     * @param int    $viewId
     * @param string $viewTypeName
     *
     * @return JsonResponse || View
     */
    public function domainDetailsSearch(Request $request, $id, $type = 'organic', $viewId = '', $viewTypeName = '')
    {
        $now = Carbon::now()->format('Y-m-d');
        //Search Keyword
        if ($viewTypeName == 'organic_keywords') {
            $searchCon = [];
            if ($request->search_url != '') {
                $searchCon[] = ['url', 'LIKE', '%' . $request->search_url . '%'];
            }
            if ($request->search_keyword != '') {
                $searchCon[] = ['keyword', 'LIKE', '%' . $request->search_keyword . '%'];
            }
            $keywords = DomainSearchKeyword::where('store_website_id', $id)->where('subtype', $type)->where('created_at', 'like', $now . '%')->where($searchCon)->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.domain-data', compact('keywords', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        //Search Traffic
        if ($viewTypeName == 'organic_traffic') {
            $searchCon = [];
            if ($request->search_url != '') {
                $searchCon[] = ['url', 'LIKE', '%' . $request->search_url . '%'];
            }
            if ($request->search_keyword != '') {
                $searchCon[] = ['number_of_keywords', 'LIKE', '%' . $request->search_keyword . '%'];
            }
            $domainorganicpage = DomainOrganicPage::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->where($searchCon)->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.domain-organic-page', compact('domainorganicpage', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        //Search Traffic
        if ($viewTypeName == 'organic_cost') {
            $searchCon = [];
            if ($request->target_url != '') {
                $searchCon[] = ['target_url', 'LIKE', '%' . $request->target_url . '%'];
            }
            if ($request->times_seen != '') {
                $searchCon[] = ['times_seen', 'LIKE', '%' . $request->times_seen . '%'];
            }
            if ($request->ads_count != '') {
                $searchCon[] = ['ads_count', 'LIKE', '%' . $request->ads_count . '%'];
            }
            $domainlandingpage = DomainLandingPage::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->where($searchCon)->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.domain-landing-page', compact('domainlandingpage', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        $compitetors = Competitor::where('store_website_id', $id)->where('created_at', 'like', $now . '%')->get();
        if (request()->ajax()) {
            return view('seo-tools.partials.domain-data', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'viewId', 'viewTypeName'));
        }

        return view('seo-tools.records', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'id', 'viewId', 'viewTypeName'));
    }

    public function backlinkDetails($id, $viewId = '', $viewTypeName = '')
    {
        $now                   = Carbon::now()->format('Y-m-d');
        $backlink_domains      = BacklinkDomains::where(['store_website_id' => $id, 'tool_id' => '1'])->where('created_at', 'like', $now . '%')->orderBy('id', 'desc')->get();
        $backlink_anchors      = BacklinkAnchors::where(['store_website_id' => $id, 'tool_id' => '1'])->where('created_at', 'like', $now . '%')->orderBy('id', 'desc')->get();
        $backlink_indexed_page = BacklinkIndexedPage::where(['store_website_id' => $id, 'tool_id' => '1'])->where('created_at', 'like', $now . '%')->orderBy('id', 'desc')->get();

        return view('seo-tools.backlinkrecords', compact('backlink_domains', 'backlink_anchors', 'id', 'backlink_indexed_page', 'viewId', 'viewTypeName'));
    }

    /**
     * This function is used to search backlink.
     *
     * @param int    $id
     * @param int    $viewId
     * @param string $viewTypeName
     *
     * @return JsonResponse
     */
    public function backlinkDetailsSearch(Request $request, $id, $viewId = '', $viewTypeName = '')
    {
        $now = Carbon::now()->format('Y-m-d');
        //Search Ascore
        if ($viewTypeName == 'ascore') {
            $searchCon = [];
            if ($request->search_database != '') {
                $searchCon[] = ['database', 'LIKE', '%' . $request->search_database . '%'];
            }
            if ($request->search_domain != '') {
                $searchCon[] = ['domain', 'LIKE', '%' . $request->search_domain . '%'];
            }
            $backlink_domains = BacklinkDomains::where(['store_website_id' => $id, 'tool_id' => 1])->where('created_at', 'like', $now . '%')->where($searchCon)->orderBy('id', 'desc')->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.backlink-data', compact('backlink_domains', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        //Search Follows
        if ($viewTypeName == 'follows_num') {
            $searchCon = [];
            if ($request->search_database != '') {
                $searchCon[] = ['database', 'LIKE', '%' . $request->search_database . '%'];
            }
            if ($request->search_anchor != '') {
                $searchCon[] = ['anchor', 'LIKE', '%' . $request->search_anchor . '%'];
            }
            $backlink_anchors = BacklinkAnchors::where(['store_website_id' => $id, 'tool_id' => 1])->where('created_at', 'like', $now . '%')->where($searchCon)->orderBy('id', 'desc')->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.backlinkanchor-data', compact('backlink_anchors', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        //search No Follows
        if ($viewTypeName == 'nofollows_num') {
            $searchCon = [];
            if ($request->source_url != '') {
                $searchCon[] = ['source_url', 'LIKE', '%' . $request->source_url . '%'];
            }
            if ($request->source_title != '') {
                $searchCon[] = ['source_title', 'LIKE', '%' . $request->source_title . '%'];
            }
            $backlink_indexed_page = BacklinkIndexedPage::where(['store_website_id' => $id, 'tool_id' => 1])->where('created_at', 'like', $now . '%')->where($searchCon)->orderBy('id', 'desc')->get();

            return response()->json([
                'tbody' => view('seo-tools.partials.backlinkindexedpage-data', compact('backlink_indexed_page', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }

        return response()->json([
            'tbody' => view('seo-tools.backlinkrecords', compact('backlink_domains', 'backlink_anchors', 'id', 'backlink_indexed_page', 'viewId', 'viewTypeName'))->render(),
        ], 200);
    }

    public function siteAudit(Request $request, $id, $viewId = '', $viewTypeName = '')
    {
        $websiteId = $id;
        $now       = Carbon::now()->format('Y-m-d');
        $siteAudit = SiteAudit::where(['store_website_id' => $id])->where($viewTypeName, '=', $viewId)->where('created_at', 'like', $now . '%')->first();

        return view('seo-tools.partials.audit-detail', compact('siteAudit', 'id', 'viewId', 'viewTypeName'))->render();
    }

    /**
     * This function use for search site audit
     *
     * @param int    $id
     * @param int    $viewId
     * @param string $viewTypeName
     *
     * @return JsonResponse
     */
    public function siteAuditSearch(Request $request, $id, $viewId = '', $viewTypeName = '')
    {
        $websiteId = $id;
        $now       = Carbon::now()->format('Y-m-d');
        $searchCon = [];
        if ($request->search_status != '') {
            $searchCon[] = ['status', 'LIKE', '%' . $request->search_status . '%'];
        }
        if ($request->search_name != '') {
            $searchCon[] = ['name', 'LIKE', '%' . $request->search_name . '%'];
        }
        $siteAudit = SiteAudit::where(['store_website_id' => $id])->where([[$viewTypeName, '=', $viewId], ['created_at', 'like', $now . '%']])->where($searchCon)->first();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('seo-tools.partials.audit-detail-search', compact('siteAudit', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }
    }
}
