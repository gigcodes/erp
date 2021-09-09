<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeoTool;
use App\StoreWebsite;
use App\DomainReport;
use App\Backlink;
use App\KeywordReport;
use App\DisplayAdvertisingReport;
use App\ReffringDomain;
use App\TrafficAnalyticsReport;
use App\UrlReport;

class SeoToolController extends Controller
{
    public function index() {
		$tools = SeoTool::select('tool', 'id')->limit(2)->get();
		$websites = StoreWebsite::pluck('website', 'id')->toArray();
		return view('seo-tools.index', compact('tools', 'websites'));
    }
	
	public function saveTool(Request $request) {
		$inputs = $request->input();
		SeoTool::create([
            'tool' => $inputs['tool'],
            'api_key' => $inputs['api_key']
        ]);
        return response()->json([
            'status' => true
        ]);
	}
	
	public function fetchDetails(Request $request) {
		//fetch domain reports
		$inputs = $request->input(); 
		$database = 'us';
		$response = [];
		$tools = SeoTool::select('tool', 'id')->limit(1)->get();
		foreach($tools as $tool) {
			$toolId = $tool['id'];
			$commonColumns = ['store_website_id'=>$inputs['websiteId'], 'tool_id'=>$toolId, 'database'=>'us'];
			
			if($toolId == 1) {
				$semrushDomainResponse = $semrushBacklinkResponse = $semrushKeywordReportResponse = $semrushDisplayAdvertisingReportResponse = $semrushReffringDomainReportResponse = $semrushTrafficAnalyticsReportResponse = [];		
				//Semrush Domain apis start
					$semrushDomainReportApis = (new DomainReport)->domainReportSemrushApis($inputs['website'], $database);  
					foreach($semrushDomainReportApis as $column=>$api) {
						$semrushDomainResponse[$column] = $this->semrushCurlRequests('domain_report', $column, $api);
						$response[$toolId]['domain_report'][$column] = $semrushDomainResponse[$column];
					}
					if(count($semrushDomainResponse) > 0) {		
						$semrushDomainResponse = $semrushDomainResponse + $commonColumns;
						DomainReport::create($semrushDomainResponse);
					}	
				//Semrush Domain apis end
				
				//Semrush Backlink apis start
					$semrushBacklinkReportApis = (new Backlink)->backlinkSemrushApis($inputs['website'], $database);  
					foreach($semrushBacklinkReportApis as $column=>$api) {
						$semrushBacklinkResponse[$column] = $this->semrushCurlRequests('backlink', $column, $api);
						$response[$toolId]['backlink'][$column] = $semrushBacklinkResponse[$column];
					}
					if(count($semrushBacklinkResponse) > 0) {		
						$semrushBacklinkResponse = $semrushBacklinkResponse + $commonColumns;
						Backlink::create($semrushBacklinkResponse);
					}	
				//Semrush Backlink apis end
				
				//Semrush Keyword apis start
					$semrushKeywordReportApis = (new KeywordReport)->keywordReportSemrushApis($inputs['website'], $database);  
					foreach($semrushKeywordReportApis as $column=>$api) {
						$semrushKeywordResponse[$column] = $this->semrushCurlRequests('keyword', $column, $api);
						$response[$toolId]['keyword_reports'][$column] = $semrushKeywordResponse[$column];
					}
					if(count($semrushKeywordResponse) > 0) {		
						$semrushKeywordReportApis = $semrushKeywordResponse + $commonColumns;
						KeywordReport::create($semrushKeywordReportApis);
					}	
				//Semrush Keyword apis end
				
				//Semrush Display Advertising apis start
                    $semrushDisplayAdvertisingReportApis = (new DisplayAdvertisingReport)->displayAdvertisingReportSemrushApis($inputs['website'], $database);
                    foreach($semrushDisplayAdvertisingReportApis as $column=>$api) {
                        $semrushDisplayAdvertisingReportResponse[$column] = $this->semrushCurlRequests('display_advertising_report', $column, $api);
                        $response[$toolId]['display_advertising_report'][$column] = $semrushDisplayAdvertisingReportResponse[$column];
                    }
                    if(count($semrushDisplayAdvertisingReportResponse) > 0) {       
                        $semrushDisplayAdvertisingReportResponse = $semrushDisplayAdvertisingReportResponse + $commonColumns;
                        DisplayAdvertisingReport::create($semrushDisplayAdvertisingReportResponse);
                    }   
                //Semrush Display Advertising apis end
				
				//Semrush Reffring Domain apis start
                    $semrushReffringDomainReportApis = (new ReffringDomain)->reffringDomainSemrushApis($inputs['website'], $database);
                    foreach($semrushReffringDomainReportApis as $column=>$api) {
                        $semrushReffringDomainReportResponse[$column] = $this->semrushCurlRequests('reffring_domain_report', $column, $api);
                        $response[$toolId]['reffring_domain_report'][$column] = $semrushReffringDomainReportResponse[$column];
                    }
                    if(count($semrushReffringDomainReportResponse) > 0) {       
                        $semrushReffringDomainReportResponse = $semrushReffringDomainReportResponse + $commonColumns;
                        ReffringDomain::create($semrushReffringDomainReportResponse);
                    }   
                //Semrush ReffringDomain apis end
				
				//Semrush Traffic Analytics apis start
                    $semrushTrafficAnalyticsReportApis = (new TrafficAnalyticsReport)->trafficanaliticsReportSemrushApis($inputs['website'], $database);
                    foreach($semrushTrafficAnalyticsReportApis as $column=>$api) {
                        $semrushTrafficAnalyticsReportResponse[$column] = $this->semrushCurlRequests('traffic_analytics_report', $column, $api);
                        $response[$toolId]['traffic_analytics_report'][$column] = $semrushTrafficAnalyticsReportResponse[$column];
                    }
                    if(count($semrushTrafficAnalyticsReportResponse) > 0) {       
                        $semrushTrafficAnalyticsReportResponse = $semrushTrafficAnalyticsReportResponse + $commonColumns;
                        TrafficAnalyticsReport::create($semrushTrafficAnalyticsReportResponse);
                    }   
                //Semrush Traffic Analytics apis end
				
				//Semrush Url Report apis start
                    $semrushUrlReportApis = (new UrlReport)->urlReportSemrushApis($inputs['website'], $database);
                    foreach($semrushUrlReportApis as $column=>$api) {
                        $semrushUrlReportResponse[$column] = $this->semrushCurlRequests('url_report', $column, $api);
                        $response[$toolId]['url_report'][$column] = $semrushUrlReportResponse[$column];
                    }
                    if(count($semrushUrlReportResponse) > 0) {       
                        $semrushUrlReportResponse = $semrushUrlReportResponse + $commonColumns;
                        UrlReport::create($semrushUrlReportResponse);
                    }   
                //Semrush Url Report apis end
			} else if($toolId == 2) {
				$ahrefDomainResponse = $ahrefBacklinkResponse = $ahrefKeywordReportResponse = $ahrefDisplayAdvertisingReportResponse = $ahrefReffringDomainReportResponse = $ahrefTrafficAnalyticsReportResponse = [];	
				
				//Ahref Reffring Domain apis start
                $ahrefReffringDomainReportApis = (new ReffringDomain)->reffringDomainAhrefApis($inputs['website'], $database);
                foreach($ahrefReffringDomainReportApis as $column=>$api) {
                    $ahrefReffringDomainReportResponse[$column] = $this->ahrefResponse('reffring_domain_report', $column, $api);
                    $response[$toolId]['reffring_domain_report'][$column] = $ahrefReffringDomainReportResponse[$column];
                }  
                if(count($ahrefReffringDomainReportResponse) > 0) {       
                    $ahrefReffringDomainReportResponse = $ahrefReffringDomainReportResponse + $commonColumns; 
                    ReffringDomain::create($ahrefReffringDomainReportResponse);
                }  
			    //Ahref Reffring Domain apis end	

				//Ahref backlink apis start
                $ahrefBacklinkReportApis = (new Backlink)->backlinkAhrefsApis($inputs['website'], $database);
                foreach($ahrefBacklinkReportApis as $column=>$api) {
                    $ahrefBacklinkResponse[$column] = $this->ahrefResponse('backlink', $column, $api);
                    $response[$toolId]['backlink'][$column] = $ahrefBacklinkResponse[$column];
                }  
                if(count($ahrefBacklinkResponse) > 0) {       
                    $ahrefBacklinkResponse = $ahrefBacklinkResponse + $commonColumns; 
                    ReffringDomain::create($ahrefBacklinkResponse);
                }  
			    //Ahref backlink apis end					
			}		
		}
		//dd($response);
		$data = view('seo-tools.records', compact('response'))->render();	
		return ['status'=>'success', 'status_code'=>200, 'response'=>$data];
	}
	
	public function semrushCurlRequests($type, $column, $api, $env="test") {
		if($env == "test") {
			if($type == "domain_report") {
				$response = (new DomainReport)->domainReportSemrushResponse($column);
			} else if($type == "backlink") {
				$response = (new Backlink)->backlinkSemrushResponse($column);
			} else if($type == "keyword") {
				$response = (new KeywordReport)->keywordReportSemrushResponse($column);
			} else if($type == "display_advertising_report") {
				$response = (new DisplayAdvertisingReport)->displayAdvertisingReportSemrushResponse($column);
			} else if($type == "reffring_domain_report") {
				$response = (new ReffringDomain)->reffringDomainSemrushResponse($column);
			} else if($type == "traffic_analytics_report") {
				$response = (new TrafficAnalyticsReport)->trafficanaliticsReportSemrushResponse($column);
			} else if($type == "url_report") {
				$response = (new UrlReport)->urlReportSemrushResponse($column);
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
	
	public function parseSemrushResponse($response) {
		$response1 = explode("\n", $response);
		$final = [];
		foreach($response1 as $new) {
			$new = explode(';', $new);
			$final[] = $new;
		}
		return json_encode($final);
	}
	
	public function ahrefResponse($type, $column, $api, $env="test") {
		if($env == "test") {
			if($type == "domain_report") {
				//$response = (new DomainReport)->domainReportSemrushResponse($column);
			} else if($type == "backlink") {
				//$response = (new Backlink)->backlinkAhrefsResponse($column);
			} else if($type == "keyword") {
				//$response = (new KeywordReport)->keywordReportSemrushResponse($column);
			} else if($type == "display_advertising_report") {
				//$response = (new DisplayAdvertisingReport)->displayAdvertisingReportSemrushResponse($column);
			} else if($type == "reffring_domain_report") {
				//$response = (new ReffringDomain)->reffringDomainAhrefResponse($column);
			} else if($type == "traffic_analytics_report") {
				//$response = (new TrafficAnalyticsReport)->trafficanaliticsReportSemrushResponse($column);
			} else if($type == "url_report") {
				//$response = (new UrlReport)->urlReportSemrushResponse($column);
			}			
		} else {
			//live code
		}
		return $this->parseAhrefResponse($response);
	}
	
	public function parseAhrefResponse($response) {	
		$response = json_decode($response, true);
		$responseFinal = [];
		foreach($response as $key=>$values) {
			if(count($values)>0) { 
				foreach($values as $i=>$records) {
					if($i == 0) {
						$responseFinal[] = array_keys($records);
					}
					$responseFinal[] = array_values($records);
				}	
     		}
		}
		return json_encode($responseFinal);
	}
}
