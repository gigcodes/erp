<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GoogleWebMasters;
use App\Http\Controllers\Controller;

use Http;

class GoogleWebMasterController extends Controller
{

	public function index() {

		$getSites =  GoogleWebMasters::all();
		return view('google-web-master/index', compact('getSites'));
		}


     public function googleLogin(Request $request)  {

            $google_redirect_url = env('APP_URL').'/googlewebmaster/get-access-token';
            $gClient = new \Google_Client();

            $gClient->setApplicationName('SoloLux-adwords-ERP');

            $gClient->setClientId('327812634030-ciu6aeb7ab2tk8tal43gv96tt2cdvkm1.apps.googleusercontent.com');

            $gClient->setClientSecret('hAze6ItSjAPydnXq-_ZZhSQL');

            $gClient->setRedirectUri($google_redirect_url);

            $gClient->setDeveloperKey('AIzaSyApzBpcN13uiCgGyEFbMpRptmsDHvwUCtw');

            $gClient->setScopes(array(
                'https://www.googleapis.com/auth/webmasters',
                'https://www.googleapis.com/auth/webmasters.readonly',
            ));

           

            $google_oauthV2 = new \Google_Service_Oauth2($gClient);
            if ($request->get('code')){
                $gClient->authenticate($request->get('code'));
                $request->session()->put('token', $gClient->getAccessToken());
            }
            if ($request->session()->get('token'))
            {
                $gClient->setAccessToken($request->session()->get('token'));
            }


        
            if ($gClient->getAccessToken())
            {

            	$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => "https://www.googleapis.com/webmasters/v3/sites",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
				    "authorization: Bearer ".$gClient->getAccessToken()['access_token']
				  ),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
					/*$curl1 = curl_init();
					//replace website name with code coming form site list
					curl_setopt_array($curl1, array(
					    CURLOPT_URL => "https://www.googleapis.com/webmasters/v3/sites/https://sololuxury.com/sitemaps?sitemapIndex=https://sololuxury.com/sitemapindex.xml",
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "GET",
					  CURLOPT_HTTPHEADER => array(
					    "authorization: Bearer ".$gClient->getAccessToken()['access_token']
					  ),
					));*/

					//$response1 = curl_exec($curl1);
					//$err = curl_error($curl1);
					
					if(is_array( json_decode( $response)->siteEntry ) ){
						foreach(json_decode( $response)->siteEntry as $key=> $site) {
							$checkSiteExist = GoogleWebMasters::where('sites',$site->siteUrl)->first(); 
							if( empty( $checkSiteExist ) ) {
								GoogleWebMasters::create(['sites'=>$site->siteUrl]);
							}else{
								GoogleWebMasters::where('sites',$site->siteUrl)->update(['sites'=>$site->siteUrl]);
							}	
						}
					}else{
						 return redirect()->route('googlewebmaster.index');  
					}
				}
                         
             return redirect()->route('googlewebmaster.index');          
            } else
            {
                //For Guest user, get google login url
                $authUrl = $gClient->createAuthUrl();
                return redirect()->to($authUrl);
            }
        }


}
