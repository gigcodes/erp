<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeoTool;
use App\StoreWebsite;
use App\DomainSearchKeyword;
use App\DomainOverview;
use App\ProjectKeyword;
use App\SemrushKeyword;
use App\SemrushTag;
use App\KeywordTag;
use App\SiteAudit;
use Carbon\Carbon;

class SeoToolController extends Controller
{
	public function index() {
		//$tools = SeoTool::select('tool', 'id')->limit(2)->get();
		$overview = DomainOverview::where(['store_website_id'=> 2, 'tool_id'=> 1])->orderBy('id', 'desc')->first();
		if($overview != null) {
			$overview['organic_keywords'] = $this->restyle_text($overview['organic_keywords']);
			$overview['organic_traffic'] = $this->restyle_text($overview['organic_traffic']);
			$overview['organic_cost'] = $this->restyle_text($overview['organic_cost']);
		}
		$websites = StoreWebsite::pluck('website', 'id')->toArray();
		
		return view('seo-tools.index', compact('keywords', 'websites', 'overview'));
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
	
	
	public function domainDetails($id, $type='organic') {
		$comp = (new DomainSearchKeyword)->competitorResponse();
		$response = $this->parseSemrushResponse($comp, 1);
		$keywords = DomainSearchKeyword::where('store_website_id', $id)->where('subtype', $type)->get();
		if (request()->ajax()) {
			return view("seo-tools.partials.domain-data", compact('keywords', 'response'));
		}
	    return view('seo-tools.records', compact('keywords', 'response'));
	}
	 
    public function fetchDetails(Request $request) {
		//fetch domain reports
		$inputs = $request->input(); 
		$database = 'us';
		$toolId = 1;
		$semrushOrganicSearchKeywordsResponse = $semrushPaidSearchKeywordsResponse = [];		
		//Semrush Domain apis start
		$semrushDomainReportApis = (new DomainSearchKeyword)->domainKeywordSearchSemrushApis($inputs['website'], $database);   
		foreach($semrushDomainReportApis as $column=>$api) {
			$semrushDomainResponse = $this->semrushCurlRequests('domain_search_keywords', $column, $api);	 
		    foreach(json_decode($semrushDomainResponse) as $value) {
				$dataToInsert = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'datebase'=>$database,'subtype'=>$column,'database'=>$database,'keyword'=>$value[0],'position'=>$value[1],'previous_position'=>$value[2],'position_difference'=>$value[3],'search_volume'=>$value[4],'cpc'=>$value[5],'url'=>$value[6],'traffic_percentage'=>$value[7],'traffic_cost'=>$value[8],'competition'=>$value[9],'number_of_results'=>$value[10], 'trends'=>$value[11]];
				DomainSearchKeyword::create($dataToInsert);
			}
		}	
	}
	
	public function semrushCurlRequests($type, $column, $api, $env="test") {
		if($env == "test") {
			if($type == "domain_search_keywords") {
				$response = (new DomainSearchKeyword)->domainKeywordSearchSemrushResponse($column);
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
		return $this->parseSemrushResponse($response);
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
	
	
	public function siteAudit($projectId) {
		$websiteId = 2;
		$now = Carbon::now()->format('Y-m-d');

		$auditLaunchApi = (new SiteAudit)->semrushApis('site_audit');
		$auditInfoApi = (new SiteAudit)->semrushApis('site_audit_info');	
		$auditInfoResponse = (new SiteAudit)->semrushApiResponses('site_audit_info');
		
		$siteAudit = SiteAudit::where('project_id', $projectId)->where('created_at', 'like', '%'.$now.'%')->first();
		

		if($siteAudit == null) {
			$data =  json_decode($auditInfoResponse, true);	
			$data = ['project_id'=>$data['id'], 'store_website_id'=>$websiteId] + $data; unset($data['id']);
			$data['defects'] = json_encode($data['defects']); 
			$data['depths'] = json_encode($data['depths']); 
			$data['markups'] = json_encode($data['markups']); 
			$data['depths'] = json_encode($data['depths']); 
			$data['mask_allow'] = json_encode($data['mask_allow']); 
			$data['mask_disallow'] = json_encode($data['mask_disallow']); 
			$data['removedParameters'] = json_encode($data['removedParameters']); 
			$siteAudit = SiteAudit::create($data);
		}
		return view('seo-tools.partials.audit-detail', compact('siteAudit'))->render();
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
