<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\DomainSearchKeyword;
use App\BacklinkAnchors;
use App\BacklinkDomains;
use App\BacklinkIndexedPage;
use App\Competitor;
use App\SiteAudit;
use Carbon\Carbon;
use App\DomainOrganicPage;
use App\DomainLandingPage;
use DB;

class DetailsController extends Controller
{
	public function compitetorsDetails($id, $type='organic') {
		$keywords = Competitor::where('store_website_id', $id)->where('subtype', $type)->get();
          if (request()->ajax()) {
			return view("seo-tools.partials.compitetors-data", compact('keywords'));
		   }		
		   return view('seo-tools.compitetorsrecords', compact('keywords', 'id'));
	}
	 
	public function domainDetails($id, $type='organic', $viewId ='', $viewTypeName ='') {
		$now = Carbon::now()->format('Y-m-d');
		$keywords = DomainSearchKeyword::where('store_website_id', $id)->where('subtype', $type)->get();
		$domainorganicpage = DomainOrganicPage::where('store_website_id', $id)->get();
		$domainlandingpage = DomainLandingPage::where('store_website_id', $id)->get();
		$compitetors = Competitor::where('store_website_id', $id)->get();
		if (request()->ajax()) {
			return view("seo-tools.partials.domain-data", compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'viewId', 'viewTypeName'));
		}
	    return view('seo-tools.records', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'id', 'viewId', 'viewTypeName'));
		//->where('created_at', 'like', $now.'%')
	}

	/**
	 * This function is use to search Domain Details
	 *
	 * @param Request $request
	 * @param int $id
	 * @param string $type
	 * @param int $viewId
	 * @param string $viewTypeName
	 * @return JsonResponse
	 */
	public function domainDetailsSearch(Request $request, $id, $type='organic', $viewId ='', $viewTypeName ='') {
		$now = Carbon::now()->format('Y-m-d');
		$keywords = DomainSearchKeyword::where('store_website_id', $id)->where('subtype', $type)->get();
		$domainorganicpage = DomainOrganicPage::where('store_website_id', $id)->get();
		$domainlandingpage = DomainLandingPage::where('store_website_id', $id)->get();
		$compitetors = Competitor::where('store_website_id', $id)->get();
		if (request()->ajax()) {
			return view("seo-tools.partials.domain-data", compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'viewId', 'viewTypeName'));
		}
	    return view('seo-tools.records', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors', 'id', 'viewId', 'viewTypeName'));
		//->where('created_at', 'like', $now.'%')
	}
	
	public function backlinkDetails($id, $viewId  = '', $viewTypeName = '') {
		$now = Carbon::now()->format('Y-m-d');
		$backlink_domains = BacklinkDomains::where(['store_website_id'=> $id, 'tool_id'=> '1'])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get(); 
		$backlink_anchors = BacklinkAnchors::where(['store_website_id'=> $id, 'tool_id'=> '1'])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get();
		$backlink_indexed_page = BacklinkIndexedPage::where(['store_website_id'=> $id, 'tool_id'=> '1'])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get();
		return view('seo-tools.backlinkrecords', compact('backlink_domains', 'backlink_anchors', 'id', 'backlink_indexed_page', 'viewId', 'viewTypeName'));
	}
	

	/**
	 * This function is used to search backlink.
	 *
	 * @param Request $request
	 * @param int $id
	 * @param int $viewId
	 * @param string $viewTypeName
	 * @return JsonResponse
	 */
	public function backlinkDetailsSearch(Request $request, $id, $viewId  = '', $viewTypeName = '') 
	{
		$now = Carbon::now()->format('Y-m-d');
		//Search Ascore
		if ($viewTypeName == 'ascore') {
			$searchCon = [];
			if ($request->search_database !='') {
				$searchCon[] = ['database', 'LIKE','%'.$request->search_database.'%'];
			}
			if ($request->search_domain !='') {
				$searchCon[] = ['domain', 'LIKE','%'.$request->search_domain.'%'];
			}
			$backlink_domains = BacklinkDomains::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->where($searchCon)->orderBy('id', 'desc')->get(); 
			dd($backlink_domains);
		    return response()->json([
                'tbody' => view('seo-tools.partials.backlink-data', compact('backlink_domains','viewId', 'viewTypeName'))->render(),
            ], 200);
        }
		
		//Search Follows
		if ($viewTypeName == 'follows_num') {
			$searchCon = [];
			if ($request->search_database !='') {
				$searchCon[] = ['database', 'LIKE','%'.$request->search_database.'%'];
			}
			if ($request->search_anchor !='') {
				$searchCon[] = ['anchor', 'LIKE','%'.$request->search_anchor.'%'];
			}
			//dd($searchCon);
			$backlink_anchors = BacklinkAnchors::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->where($searchCon)->orderBy('id', 'desc')->get();
			
		    return response()->json([
                'tbody' => view('seo-tools.partials.backlinkanchor-data', compact('backlink_anchors','viewId', 'viewTypeName'))->render(),
            ], 200);
        }

		//search No Follows
		if ($viewTypeName == 'nofollows_num') {
			$searchCon = [];
			if ($request->source_url !='') {
				$searchCon[] = ['source_url', 'LIKE','%'.$request->source_url.'%'];
			}
			if ($request->source_title !='') {
				$searchCon[] = ['source_title', 'LIKE','%'.$request->source_title.'%'];
			}
			//dd($searchCon);
			$backlink_indexed_page = BacklinkIndexedPage::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->where($searchCon)->orderBy('id', 'desc')->get();
			
		    return response()->json([
                'tbody' => view('seo-tools.partials.backlinkindexedpage-data', compact('backlink_indexed_page','viewId', 'viewTypeName'))->render(),
            ], 200);
        }
		
		return response()->json([
			'tbody' => view('seo-tools.backlinkrecords', compact('backlink_domains', 'backlink_anchors', 'id', 'backlink_indexed_page', 'viewId', 'viewTypeName'))->render(),
		], 200);
	}

	public function siteAudit(Request $request, $id, $viewId  = '', $viewTypeName = '') {
		$websiteId = $id;
		$now = Carbon::now()->format('Y-m-d');
	    //DB::enableQueryLog(); // Enable query log
		$siteAudit = SiteAudit::where(['store_website_id'=> $id])->where($viewTypeName, '=', $viewId)->where('created_at', 'like', $now.'%')->first();
		//dd(DB::getQueryLog()); // Show results of log
		return view('seo-tools.partials.audit-detail', compact('siteAudit', 'id', 'viewId', 'viewTypeName'))->render();
		//->where('created_at', 'like', $now.'%')
	}

	/**
	 * This function use for search site audit
	 *
	 * @param Request $request
	 * @param int $id
	 * @param int $viewId
	 * @param string $viewTypeName
	 * @return JsonResponse
	 */
	public function siteAuditSearch(Request $request, $id, $viewId  = '', $viewTypeName = '') 
	{
		$websiteId = $id;
		$now = Carbon::now()->format('Y-m-d');
		$searchCon = [];
		if($request->search_status !='') {
			$searchCon = ['status', 'LIKE','%'.$request->search_status.'%'];
		}
	    if($request->search_name !='') {
			$searchCon = ['name', 'LIKE','%'.$request->search_name.'%'];
		}
	    $siteAudit = SiteAudit::where(['store_website_id'=> $id])->where([[$viewTypeName, '=', $viewId], ['created_at', 'like', $now.'%' ], $searchCon])->first();
		if ($request->ajax()) {
            return response()->json([
                'tbody' => view('seo-tools.partials.audit-detail-search', compact('siteAudit', 'viewId', 'viewTypeName'))->render(),
            ], 200);
        }
	}
}

