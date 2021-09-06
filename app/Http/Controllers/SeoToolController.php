<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeoTool;
use App\StoreWebsite;
use App\DomainSearchKeyword;
use App\DomainOverview;
use App\DomainLandingPage;
use App\BacklinkOverview;
use App\BacklinkAnchors;
use App\BacklinkDomains;
use App\Competitor;
use App\SiteIssue;
use App\SiteAudit;
use App\DomainOrganicPage;
use App\BacklinkIndexedPage;
use App\ProjectKeyword;
use App\SemrushKeyword;
use App\SemrushTag;
use App\KeywordTag;
use Carbon\Carbon;

class SeoToolController extends Controller
{
	public function index() {
		$websites = StoreWebsite::pluck('website', 'id')->toArray();
		$siteAudits = $domainOverview = $backlinkreports = [];
		foreach($websites as $websiteId=>$website) { 
			$siteAuditDetail = SiteAudit::where(['store_website_id'=>$websiteId])->orderBy('id', 'desc')->first();
			if($siteAuditDetail != null) {
				$siteAudits[$websiteId]['errors'] = $siteAuditDetail['errors'];
				$siteAudits[$websiteId]['warnings'] = $siteAuditDetail['warnings'];
				$siteAudits[$websiteId]['pages_crawled'] = $siteAuditDetail['pages_crawled'];
			}
			$overview = DomainOverview::where(['store_website_id'=>$websiteId, 'tool_id'=> 1])->orderBy('id', 'desc')->first();
			if($overview != null) {
				$domainOverview[$websiteId]['organic_keywords'] = $this->restyle_text($overview['organic_keywords']);
				$domainOverview[$websiteId]['organic_traffic'] = $this->restyle_text($overview['organic_traffic']);
				$domainOverview[$websiteId]['organic_cost'] = $this->restyle_text($overview['organic_cost']);
			}
			$backlinkreport = BacklinkOverview::where(['store_website_id'=>$websiteId, 'tool_id'=> 1])->orderBy('id', 'desc')->first();
			if($backlinkreport != null) {
				$backlinkreports[$websiteId]['ascore'] = $this->restyle_text($backlinkreport['ascore']);
				$backlinkreports[$websiteId]['follows_num'] = $this->restyle_text($backlinkreport['follows_num']);
				$backlinkreports[$websiteId]['nofollows_num'] = $this->restyle_text($backlinkreport['nofollows_num']);
			}
		}
		
		
		return view('seo-tools.index', compact('keywords', 'websites', 'siteAudits', 'domainOverview', 'backlinkreports'));
    }
	
	public function restyle_text($input) {
		$input = number_format($input);
		$input_count = substr_count($input, ',');
		if($input_count != '0'){
			if($input_count == '1'){
				return substr($input, 0, -4).' K';
			} else if($input_count == '2'){
				return substr($input, 0, -8).' M';
			} else if($input_count == '3'){
				return substr($input, 0,  -12).' B';
			} else {
				return;
			}
		} else {
			return $input;
		}
	}
	
    public function fetchDetails() {
		//fetch domain reports
		$storeWebsites = StoreWebsite::select('id', 'website')->get();
		foreach($storeWebsites as $storeWebsite) {
		$inputs['website'] = $storeWebsite['website'];
		$inputs['websiteId'] =  $websiteId =$storeWebsite['id'];
		$database = 'us';
		$toolId = 1;
		$now = Carbon::now()->format('Y-m-d');

		$semrushOrganicSearchKeywordsResponse = $semrushPaidSearchKeywordsResponse = $semrushBacklinkOverviewResponse = $semrushBacklinkAnchorResponse = $semrushBacklinkDomainResponse=$semrushCompetitorResponse=[];		
		//Semrush Domain apis start
		$semrushDomainReportApis = (new DomainSearchKeyword)->domainKeywordSearchSemrushApis($inputs['website'], $database);   
		foreach($semrushDomainReportApis as $column=>$api) {
			$semrushDomainResponse = $this->semrushCurlRequests('domain_search_keywords', $column, $api);	 
			$domainSearchKeywords = DomainSearchKeyword::where('store_website_id', $websiteId)->where('created_at', 'like', '%'.$now.'%')->where('subtype',$column)->first();	
			if($domainSearchKeywords == null) { 
				foreach(json_decode($semrushDomainResponse) as $value) {
					$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column,'database'=>$database,'keyword'=>$value[0],'position'=>$value[1],'previous_position'=>$value[2],'position_difference'=>$value[3],'search_volume'=>$value[4],'cpc'=>$value[5],'url'=>$value[6],'traffic_percentage'=>$value[7],'traffic_cost'=>$value[8],'competition'=>$value[9],'number_of_results'=>$value[10], 'trends'=>$value[11]];
					DomainSearchKeyword::create($dataToInsert);
				}				
			}
		}

		//Semrush Domain Overview apis start
		$semrushDomainReportApi = (new DomainOverview)->domainOverviewSemrushApis($inputs['website'], $database, 'overview_all');   
		$semrushDomainOverviewResponse = $this->semrushCurlRequests('domain_overview', 'overview_all', $semrushDomainReportApi, $keyValuePair=1); 
		$domainOverviewData = DomainOverview::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first();
		if($domainOverviewData == null) {
			foreach(json_decode($semrushDomainOverviewResponse, true) as $value) { 
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'rank'=>$value['rank'],'organic_keywords'=>$value['organic_keywords'],'organic_traffic'=>$value['organic_traffic'],'organic_cost'=>$value['organic_cost'],'adwords_keywords'=>$value['adwords_keywords'],'adwords_traffic'=>$value['adwords_traffic'],'adwords_cost'=>$value['adwords_cost'],'pla_keywords'=>$value['pla_keywords'],'pla_uniques'=>$value['pla_uniques']];
				DomainOverview::create($dataToInsert);
			}
		}
        
         //Semrush Backlink apis Start
		$semrushBacklinkOverviewReportApi = (new BacklinkOverview)->backlinkoverviewSemrushApis($inputs['website'], $database);   
		$semrushBacklinkOverviewResponse = $this->semrushCurlRequests('backlink_overview','overview', $semrushBacklinkOverviewReportApi, 1);
		    $backlinkOverviewData = BacklinkOverview::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first(); 
			if($backlinkOverviewData  == null){
				foreach(json_decode($semrushBacklinkOverviewResponse, true) as $value) { 
					$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database, 'ascore'=>$value['ascore'], 'total'=>$value['total'], 'domains_num'=>$value['domains_num'], 'urls_num'=>$value['urls_num'], 'ips_num'=>$value['ips_num'], 'ipclassc_num'=>$value['ipclassc_num'], 'follows_num'=>$value['follows_num'], 'nofollows_num'=>$value['nofollows_num'], 'sponsored_num'=>$value['sponsored_num'], 'ugc_num'=>$value['ugc_num'], 'texts_num'=>$value['texts_num'], 'images_num'=>$value['images_num'], 'forms_num'=>$value['forms_num'], 'frames_num'=>$value['frames_num']];
					BacklinkOverview::create($dataToInsert);
				}
			}
		       
		//Semrush Backlink Anchor Api Start
        $semrushBacklinkAnchorReportApi = (new BacklinkAnchors)->backlinkanchorsSemrushApis($inputs['website'], $database);   
		$semrushBacklinkAnchorResponse = $this->semrushCurlRequests('back_link_anchors','anchor', $semrushBacklinkAnchorReportApi, 1);
		$backlinkAnchorData = BacklinkAnchors::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first(); 
		if($backlinkAnchorData  == null){
			foreach(json_decode($semrushBacklinkAnchorResponse, true) as $value) { 
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'anchor'=>$value['anchor'],'domains_num'=>$value['domains_num'],'backlinks_num'=>$value['backlinks_num']];
				BacklinkAnchors::create($dataToInsert);
			}
		}

        //Semrush Backlink Domain Api Start
        $semrushBacklinkDomainReportApis = (new BacklinkDomains)->backlinkdomainsSemrushApis($inputs['website'], $database);   
		foreach($semrushBacklinkDomainReportApis as $column=>$api) {
			$backlinkDomainsData = BacklinkDomains::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column])->where('created_at', 'like', '%'.$now.'%')->first(); 
			if($backlinkDomainsData  == null) {
				$semrushBacklinkDomainResponse = $this->semrushCurlRequests('back_link_domains',$column, $api, 1);	
				foreach(json_decode($semrushBacklinkDomainResponse, true) as $value) { 
					$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column,'domain'=>$value['domain'],'domain_ascore'=>$value['domain_ascore'],'backlinks_num'=>$value['backlinks_num']];
					 BacklinkDomains::create($dataToInsert);
				}
			}
		}

	    //Semrush Competitor Api Start
		$semrushCompetitorReportApis = (new Competitor)->competitorSemrushApis($inputs['website'], $database);   
		foreach($semrushCompetitorReportApis as $column=>$api) {
			$competitorData = Competitor::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column])->where('created_at', 'like', '%'.$now.'%')->first(); 
			if($competitorData  == null) {
				$semrushCompetitorResponse = $this->semrushCurlRequests('competitors',$column, $api, 1);
				foreach(json_decode($semrushCompetitorResponse, true) as $value) { 
					if($column == 'paid') {
						$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column,'domain'=>$value['domain'],'common_keywords'=>$value['common_keywords'],'keywords'=>$value['adwords_keywords'],'traffic'=>$value['adwords_traffic']];
					} else{
						$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database,'subtype'=>$column,'domain'=>$value['domain'],'common_keywords'=>$value['common_keywords'],'keywords'=>$value['organic_keywords'],'traffic'=>$value['organic_traffic']];
					}
					Competitor::create($dataToInsert);
				}
			}
		}
				       
		//Semrush OrganicPage Api Start
        $semrushOrganicPageApi = (new DomainOrganicPage)->organicPageSemrushApi($inputs['website'], $database);   
		$semrushOrganicPageResponse = $this->semrushCurlRequests('organic_page','organic_page', $semrushOrganicPageApi, 1);
		$organicPageData = DomainOrganicPage::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first(); 
		if($organicPageData == null){
			foreach(json_decode($semrushOrganicPageResponse, true) as $value) {
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 
				'database'=>$database,'url'=>$value['url'], 'number_of_keywords'=>$value['number_of_keywords'], 'traffic'=>$value['traffic'], 'traffic_percentage'=>$value['traffic_']];
				DomainOrganicPage::create($dataToInsert);
			}
		}
		
		//Semrush Indexed Page Api Start
        $semrushIndexedPageApi = (new BacklinkIndexedPage)->indexedPageSemrushApi($inputs['website'], $database, 'indexed_page');   
		$semrushIndexedPageResponse = $this->semrushCurlRequests('indexed_page','indexed_page', $semrushIndexedPageApi, 1);
		$indexedPageData = BacklinkIndexedPage::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first(); 
		if($indexedPageData == null){
			foreach(json_decode($semrushIndexedPageResponse, true) as $value) {
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 
				'database'=>$database,'source_url'=>$value['source_url'], 'source_title'=>$value['source_title'], 
				'response_code'=>$value['response_code'], 'backlinks_num'=>$value['backlinks_num'], 
				'domains_num'=>$value['domains_num'],'last_seen'=>$value['last_seen'],'external_num'=>$value['external_num'],'internal_num'=>$value['internal_num']];
				BacklinkIndexedPage::create($dataToInsert);
			}
		}
		//Semrush Landing Page Api Start
        $semrushLandingPageApi = (new DomainLandingPage)->landingPageSemrushApi($inputs['website'], $database, 'landing_page');   
		$semrushLandingPageResponse = $this->semrushCurlRequests('landing_page','landing_page', $semrushLandingPageApi, 1);
		$LandingPageData = DomainLandingPage::where(['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>$database])->where('created_at', 'like', '%'.$now.'%')->first(); 
		if($LandingPageData == null){ 
			foreach(json_decode($semrushLandingPageResponse, true) as $value) { 
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 
				'database'=>$database,'target_url'=>$value['target_url'], 'first_seen'=>$value['first_seen'], 
				'last_seen'=>$value['last_seen'], 'times_seen'=>$value['times_seen'], 
				'ads_count'=>$value['ads_count']];
				DomainLandingPage::create($dataToInsert);
			}
		}
						       
		
		//Semrush siteaudit api start
		$websiteId = $inputs['websiteId'];
		$projectId = StoreWebsite::where('id', $websiteId)->pluck('semrush_project_id')->first();
		$projectId  = 2135454;
		if($projectId  != null){
			
			$auditLaunchApi = (new SiteAudit)->semrushApis('site_audit');
			
			$auditInfoApi = (new SiteAudit)->semrushApis('site_audit_info');	
			$auditInfoResponse = (new SiteAudit)->semrushApiResponses('site_audit_info');
			
			$siteIssuesApi = (new SiteAudit)->semrushApis('site_issues');
			$siteIssuesResponse = (new SiteAudit)->semrushApiResponses('site_issues');
			
			$data = json_decode($auditInfoResponse, true);				
			$siteAudit = SiteAudit::where('store_website_id', $websiteId)->where('created_at', 'like', '%'.$now.'%')->first();	
			if($siteAudit == null) {
				$data = ['project_id'=>$projectId, 'store_website_id'=>$websiteId] + $data; unset($data['id']);
				$data['defects'] = json_encode($data['defects']); 
				$data['depths'] = json_encode($data['depths']); 
				$data['markups'] = json_encode($data['markups']); 
				$data['depths'] = json_encode($data['depths']); 
				$data['mask_allow'] = json_encode($data['mask_allow']); 
				$data['mask_disallow'] = json_encode($data['mask_disallow']); 
				$data['removedParameters'] = json_encode($data['removedParameters']);
				if($data['excluded_checks'] == null) {
					$data['excluded_checks'] = 0;
				}
				$siteAudit = SiteAudit::create($data);
			}	
			$siteAuditIssues = json_decode($siteIssuesResponse, true);
			foreach($siteAuditIssues['issues'] as $siteAuditIssuesData) { //dd($siteAuditIssuesData);
				$dataToSave = ['project_id'=>$projectId, 'store_website_id'=>$websiteId];
				$dataToSave['title'] = $siteAuditIssuesData['title'];
				$dataToSave['desc'] = $siteAuditIssuesData['desc'];
				$dataToSave['title_page'] = $siteAuditIssuesData['title_page'];
				SiteIssue::create($dataToSave);
			}
		}
		}
		return redirect('seo');
	}
	
	public function semrushCurlRequests($type, $column, $api, $keyValuePair=0,$env="test") {
		if($env == "test") {
			if($type == "domain_search_keywords") {
				$response = (new DomainSearchKeyword)->domainKeywordSearchSemrushResponse($column);
			} else if($type == "domain_overview") { 
                $response = (new DomainOverview)->domainOverviewSemrushResponse($column);
            } else if($type == "backlink_overview") { 
                $response = (new BacklinkOverview)->backlinkoverviewSemrushResponse($column);
            }else if($type == "back_link_anchors") { 
                $response = (new BacklinkAnchors)->backlinkanchorsSemrushResponse($column);
            } else if($type == "back_link_domains") { 
                $response = (new BacklinkDomains)->backlinkdomainsSemrushResponse($column);
            } else if($type == "competitors") { 
                $response = (new Competitor)->competitorSemrushResponse($column);
            }else if($type == "organic_page") { 
                $response = (new DomainOrganicPage)->organicPageSemrushResponse($column);
            } else if($type == "indexed_page") { 
                $response = (new BacklinkIndexedPage)->indexedPageSemrushResponse($column);
            }
              else if($type == "landing_page") { 
                $response = (new DomainLandingPage)->landingPageSemrushResponse($column);
            }			
		} else {
			$semrushApis = '';
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $api,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			));
			$response = curl_exec($curl);
			curl_close($curl);
		}
		if($keyValuePair==1) {
			return $this->parseSemrushResponseKeyValue($response);
		} else{
			return $this->parseSemrushResponse($response);
		}
		
	}
	
	public function parseSemrushResponse($response, $heading=0) {
		$response1 = explode("\n", $response);
		$final = [];
		foreach($response1 as $key=>$new) {
			if( $heading == 0) {
				if($key > 0) {
					$new = explode(';', $new);
					$final[] = $new;
				}
			} else{
				$new = explode(';', $new);
					$final[] = $new;
			}
			
		}
		return json_encode($final);
	}
	
	public function parseSemrushResponseKeyValue($response) {
		$response1 = explode("\n", $response);
		$final = [];
		foreach($response1 as $new) {
			$new = explode(';', $new);
			$final[] = $new;
		}
		$arrayToBeUsed = [];
		$heading = $final[0];
		unset($final[0]);
		$i = 0;
		foreach($final as $keydata) {
			foreach($keydata as $key=>$value) {
				$headingKey= str_replace(' ', '_', $heading[$key]); 
			 $headingKey = strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $headingKey));
				$arrayToBeUsed[$i][$headingKey] = $value;
			}
			$i++;
		} 
		return json_encode($arrayToBeUsed );
	}

	public function saveKeyword(Request $request) { 
		$response = '{
			"url": "mysite.com",
			"keywords": [
			{
			"keyword": "search tool",
			"tags": ["search"],
			"timestamp": 1391517755
			},
			{
			"keyword": "search engine",
			"tags": ["search"],
			"timestamp": 1391517755
			},
			{
			"keyword": "seo",
			"tags": ["seo"],
			"timestamp": 1491517755
			},
			{
			"keyword": "seotool",
			"tags": ["seo"],
			"timestamp": 1491517755
			}
			],
			"competitors": ["google.com","ebay.com","bing.com"],
			"tools": [],
			"project_id": 643526670283248,
			"project_name": "my old project"
			}';
		$result = json_decode($response, true);
		foreach($result['keywords'] as $keywordDetail) { 
			$keywordDetailNew = SemrushKeyword::firstOrCreate(['keyword'=> $keywordDetail['keyword']], ['keyword'=> $keywordDetail['keyword']]);	
			foreach($keywordDetail['tags'] as $tag) {
				$tagDetail = SemrushTag::firstOrCreate(['tag'=>$tag], ['tag'=>$tag]);
				KeywordTag::firstOrCreate(['keyword_id'=>$keywordDetailNew['id'], 'tag_id'=>$tagDetail['id']], 
				['keyword_id'=>$keywordDetailNew['id'], 'tag_id'=>$tagDetail['id']]);
			}

			ProjectKeyword::firstOrCreate(['keyword_id'=>$keywordDetailNew['id'], 'project_id'=>$request->projectId], ['keyword_id'=>$keywordDetailNew['id'], 'project_id'=>$request->projectId]);
		}
		return redirect(url('seo/project-list'));
		//SemrushKeyword::create(['keyword'=>]);
	}

	public function projectList() { 
		$projectListApi = (new SiteAudit)->semrushApis('project_list');
		$auditInfoResponse = (new SiteAudit)->semrushApiResponses('project_list');
		$project = json_decode($auditInfoResponse, true);
		return view('seo-tools.projects', compact('project')); 
	}
	
	public function semrushApis($api_type, $projectId) {
		$apis = [
			'site_audit'=>'https://api.semrush.com/reports/v1/projects/{ID}/siteaudit/launch?key=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
		];
	}
	
	
	
}
