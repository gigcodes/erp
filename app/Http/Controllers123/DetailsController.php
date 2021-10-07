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

class DetailsController extends Controller
{
	public function compitetorsDetails($id, $type='organic') {
		$keywords = Competitor::where('store_website_id', $id)->where('subtype', $type)->get();
          if (request()->ajax()) {
			return view("seo-tools.partials.compitetors-data", compact('keywords'));
		   }		
		   return view('seo-tools.compitetorsrecords', compact('keywords', 'id'));
	}
	 
	public function domainDetails($id, $type='organic') {
		$now = Carbon::now()->format('Y-m-d');
		$keywords = DomainSearchKeyword::where('store_website_id', $id)->where('created_at', 'like', '%'.$now.'%')->where('subtype', $type)->get();
		$domainorganicpage = DomainOrganicPage::where('store_website_id', $id)->where('created_at', 'like', '%'.$now.'%')->get();
		$domainlandingpage = DomainLandingPage::where('store_website_id', $id)->where('created_at', 'like', '%'.$now.'%')->get();
		$compitetors = Competitor::where('store_website_id', $id)->where('created_at', 'like', '%'.$now.'%')->get();
		if (request()->ajax()) {
			return view("seo-tools.partials.domain-data", compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors'));
		}
	    return view('seo-tools.records', compact('keywords', 'domainorganicpage', 'domainlandingpage', 'compitetors'));
	}
	
	public function backlinkDetails($id) {
		$now = Carbon::now()->format('Y-m-d');
		$backlink_domains = BacklinkDomains::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get(); 
		$backlink_anchors = BacklinkAnchors::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get();
		$backlink_indexed_page = BacklinkIndexedPage::where(['store_website_id'=> $id, 'tool_id'=> 1])->where('created_at', 'like', $now.'%')->orderBy('id', 'desc')->get();
		return view('seo-tools.backlinkrecords', compact('backlink_domains', 'backlink_anchors', 'id', 'backlink_indexed_page'));
	}
	
	
	  public function siteAudit(Request $request,$id) {
		$websiteId = $id;
		$now = Carbon::now()->format('Y-m-d');
	    $siteAudit=SiteAudit::where(['store_website_id'=> $id])->where('created_at', 'like', $now.'%')->first();
		return view('seo-tools.partials.audit-detail', compact('siteAudit', 'id'))->render();
	}
}

