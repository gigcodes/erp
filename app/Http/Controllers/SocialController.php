<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Facebook\Facebook;

class SocialController extends Controller
{
    //
	private $fb,$user_access_token,$page_access_token,$page_id,$ad_acc_id;

	public function __construct(Facebook $fb)
	{
		$this->fb=$fb;
		$this->user_access_token="EAAD7Te0j0B8BALlNg4kgCX0d4n4EDjK36IlIfAs1rDmEyw9QyVkyoVWWb76QGdZBno3udtap2TZA7XWBmXuZAZBGOJJKBPky5VoQccoUOewqWB8mVwr0ZC6nyHsmZAQyYZCrKPQStP41mtWioDfmBEfndwtuK8ZCcjSwbEpUNyRNhqPSw3UZACZCAOHTfzWKXittUZD";


		$this->page_access_token="EAAD7Te0j0B8BALZAk0kYeVovJtMZCuGYFGpbAfsF8bwYCgR7EUh71mng1Qozat4Gykq1ZCZCT3Uov2p8HzqAVjcfueBeB5mmx1fNvGjp2oo8NqUY93avDxpeCX2xYCwkoZBIxAKX2wbEVnTkSnDZCbCEUCIf7Q8TvQXuqWMAu2ELSk2ab2NQjbkjhkRp7Dm6sZD";
		$this->page_id="507935072915757";
		$this->ad_acc_id="act_128125721296439";


        // These are for testing purpose...
//        $this->user_access_token="EAAD7Te0j0B8BAFRNcoNM6Ofde6tFe6nkmy1Ak4CBhKi2uKO74VBIhZAieyRlGTyRNMcghZB4ado2JOXQZChsdZCTjopbQ633mwaDJuROXI3cXchrPU1PM2FLzHJL0FyGfA01S6P4ZB5FQ8F0WwgtNeIJfSJHu3vOZC5JYCd2ZCYgzN2raWhA0yZBPpd8pb6mgdsZD";
//        $this->page_access_token="EAAD7Te0j0B8BALZBZAPZBvlnxK5E6zA5p8zXfsO39rZAdjk9jY6YSFdxZBUi2Xe1A6gkdkGB7RyL9P8xJ6n192Lv9esvjbTq2E6g0k7aiySH9HLz5dRjRM2dMx6ZBN4KXIHEUOWpomcYmAG99MgWeV9It54CNb1TIbB1cuEBfGyrzW4CJnZClwbWNMlTxnbjPbZAfNeKGPB2EwZDZD";

		
		
		
	}
	public function index()
	{
		return view('social.post');
	}


	// public function for getting Social Page posts

	public function pagePost(Request $request)
	{
		if($request->input('next') && !empty($request->input('next')))
		{

			$data['posts']=substr($request->input('next'),32);
			$data['posts']=$this->fb->get($data['posts'])->getGraphEdge();




			
		}
		elseif($request->input('previous') && !empty($request->input('previous')))
		{
			$data['posts']=substr($request->input('previous'),32);
			$data['posts']=$this->fb->get($data['posts'])->getGraphEdge();
		}
		else
		{
			$data['posts']=$this->fb->get(''.$this->page_id.'/feed?fields=id,full_picture,permalink_url,name,description,message,created_time,from,story,likes.limit(0).summary(true),comments.summary(true)&limit=10&access_token='.$this->page_access_token.'')->getGraphEdge();
		}

		// Making Pagination


		if(isset($data['posts']->getMetaData()['paging']['next']) && !empty($data['posts']->getMetaData()['paging']['next']))
			$data['next']=$data['posts']->getMetaData()['paging']['next'];

		if(isset($data['posts']->getMetaData()['paging']['previous']) && !empty($data['posts']->getMetaData()['paging']['previous']))
			$data['previous']=$data['posts']->getMetaData()['paging']['previous'];


		// Getting Final Result as Array
        $data['posts'] = $data['posts']->all();
		$data['posts']=array_map(function ($post) {
		    $post = $post->all();
		    return [
		        'id' => $post['id'],
		        'full_picture' => $post['full_picture'] ?? null,
		        'permalink_url' => $post['permalink_url'] ?? null,
		        'name' => $post['name'] ?? 'N/A',
		        'message' => $post['message'] ?? null,
		        'created_time' => $post['created_time'],
		        'from' => $post['from'],
		        'likes' => [
		            'summary' => $post['likes']->getMetaData()['summary']
                ],
		        'comments' => [
		            'summary' => $post['comments']->getMetaData()['summary'],
                    'items' => $post['comments']->asArray()
                ],
            ];
        }, $data['posts']);

		return view('social.get-posts',$data);
	}




	// Creating posts to page via sdk

	public function createPost(Request $request)
	{
		$request->validate([
			'message' => 'required',
			'source.*' => 'mimes:jpeg,bmp,png,gif,tiff',
			'video' =>'mimes:3g2,3gp,3gpp,asf,avi,dat,divx,dv,f4v,flv,gif,m2ts,m4v,mkv,mod,mov,mp4,mpe, mpeg,mpeg4,mpg,mts,nsv,ogm,ogv,qt,tod,tsvob,wmv',

		]);






		// Message
		$message=$request->input('message');



		// Image  Case


		if ($request->hasFile('source'))
		{
			// Description
			$data['caption']=($request->input('description'))?$request->input('description'):"";
			$data['published']="false";
			$data['access_token']=$this->page_access_token;

			foreach($request->file('source') as $key =>$source)
			{
				$data['source']=$this->fb->fileToUpload($source);

					// post multi-photo story
				$multiPhotoPost['attached_media['.$key.']'] ='{"media_fbid":"'.$this->fb->post('/me/photos', $data)->getGraphNode()->asArray()['id'].'"}';
			}

			// Uploading Multi story facebook photo
			$multiPhotoPost['access_token']=$this->page_access_token;
			$multiPhotoPost['message']=$message;
			if($request->has('date') && $request->input('date')>date('Y-m-d'))
			{
				$multiPhotoPost['published']="false";
				$multiPhotoPost['scheduled_publish_time']=strtotime($request->input('date'));
			}
			$resp = $this->fb->post('/me/feed',$multiPhotoPost)->getGraphNode()->asArray();
			if(isset($resp->error->message))
				Session::flash('message',$resp->error->message); 
			else
				Session::flash('message',"Content Posted successfully"); 


			return redirect()->route('social.post.page');
		}





		// Video Case
		elseif($request->hasFile('video'))
		{

			$data['title'] ="". trim($message)."";



			$data['description'] = "".trim($request->input('description'))."";


			$data['source']=$this->fb->videoToUpload("".trim($request->file('video'))."");
			// dd($thumb);






			if($request->has('date') && $request->input('date')>date('Y-m-d'))
			{
				$data['published']="false";
				$data['scheduled_publish_time']=strtotime($request->input('date'));
			}
			$resp= $this->fb->post('/me/videos', $data,$this->page_access_token)->getGraphNode()->asArray()['id'];

			if(isset($resp->error->message))
				Session::flash('message',$resp->error->message); 
			else
				Session::flash('message',"Content Posted successfully"); 


			return redirect()->route('social.post.page');

		}


		// Simple Post Case

		else
		{

			$data['description']=$request->input('description');
			$data['message']=$message;
			$data['access_token']=$this->page_access_token;
			if($request->has('date') && $request->input('date')>date('Y-m-d'))
			{
				$data['published']="false";
				$data['scheduled_publish_time']=strtotime($request->input('date'));
			}
			$resp = $this->fb->post('/me/feed',$data)->getGraphNode()->asArray();

			if(isset($resp->error->message))
				Session::flash('message',$resp->error->message); 
			else
				Session::flash('message',"Content Posted successfully"); 


			return redirect()->route('social.post.page');

		}


	}

	// Function for Getting Reports via curl
	public function report()
	{



		$query="https://graph.facebook.com/v3.2/".$this->ad_acc_id."/campaigns?fields=ads{id,name,status,created_time,adcreatives{thumbnail_url},adset{name},insights.level(adset){campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend}}&limit=30&access_token=".$this->user_access_token."";


			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);

		curl_close($ch);
		if(isset($resp->error->error_user_msg))
			Session::flash('message',$resp->error->error_user_msg); 
		elseif(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 


		return view('social.reports',['resp'=>$resp]);
	}




	// Get pagination Report()

	public function paginateReport(Request $request) 
	{
		if($request->has('next'))
			$query=$request->input('next');
		elseif($request->has('previous'))
			$query=$request->input('previous');
		else 
			return redirect()->route('social.report');






			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);
		curl_close($ch);
		if(isset($resp->error->error_user_msg))
			Session::flash('message',$resp->error->error_user_msg); 
		elseif(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 


		return view('social.reports',['resp'=>$resp]);
	}



	// Getting reports for adCreative

	// Function for Getting Reports via curl
	public function adCreativereport()
	{



		$query="https://graph.facebook.com/v3.2/".$this->ad_acc_id."/campaigns?fields=ads{adcreatives{id,name,thumbnail_url},insights.level(ad).metrics(ctr){cost_per_unique_click,spend,impressions,frequency,reach,unique_clicks,clicks,ctr,ad_name,adset_name,cpc,cpm,cpp,campaign_name,ad_id,adset_id,account_id,account_name}}&access_token=".$this->user_access_token."";


			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);
		curl_close($ch);
		if(isset($resp->error->error_user_msg))
			Session::flash('message',$resp->error->error_user_msg); 
		elseif(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 


		return view('social.adcreative-reports',['resp'=>$resp]);
	}
	// end of getting reports via ad creatvie


	// paginate ad creative report
	public function adCreativepaginateReport(Request $request) 
	{
		if($request->has('next'))
			$query=$request->input('next');
		elseif($request->has('previous'))
			$query=$request->input('previous');
		else 
			return redirect()->route('social.report');






			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);
		curl_close($ch);
		if(isset($resp->error->error_user_msg))
			Session::flash('message',$resp->error->error_user_msg); 
		elseif(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 


		return view('social.adcreative-reports',['resp'=>$resp]);
	}

	// end of paginate ad  creative report
	




	// Changing Ad status via curl
	public function changeAdStatus($ad_id,$status)
	{

		$data['access_token']=$this->user_access_token;
		$data['status']=$status;


		$url="https://graph.facebook.com/v3.2/".$ad_id;


			// Call to Graph api here
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


		$resp = curl_exec($curl);
		$resp = json_decode($resp);
		curl_close($curl);
		if(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 
		else
			Session::flash('message',"Status changed successfully"); 


		return redirect()->route('social.report');

	}



	// Creating New Campaign via curl

	public function createCampaign()
	{
		return view('social.campaign');
	}

	// For storing campaign to fb via curl

	public function storeCampaign(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'objective' => 'required',
			'status' =>'required',
		]);

		$data['name']=$request->input('name');
		$data['objective']=$request->input('objective');
		$data['status']=$request->input('status');


		if($request->has('buying_type'))
			$data['buying_type']=$request->input('buying_type');
		else
			$data['buying_type']='AUCTION';

		if($request->has('daily_budget'))
			$data['daily_budget']=$request->input('daily_budget');





		// Storing to fb via curl

		try{
			$data['access_token']=$this->user_access_token;



			$url="https://graph.facebook.com/v3.2/".$this->ad_acc_id.'/campaigns';


			// Call to Graph api here
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


			$resp = curl_exec($curl);
			$resp = json_decode($resp);
			curl_close($curl);
			if(isset($resp->error->message))
				Session::flash('message',$resp->error->message); 
			else
				Session::flash('message',"Campaign created  successfully"); 


			return redirect()->route('social.ad.campaign.create');
		}
		catch(Exception $e)
		{
			Session::flash('message',$e); 
			return redirect()->route('social.ad.campaign.create');
		}

	}


	// Creating New Campaign via curl

	public function createAdset()
	{

		$query="https://graph.facebook.com/v3.2/".$this->ad_acc_id."/campaigns?fields=name,id&limit=100&access_token=".$this->user_access_token."";



			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);

		curl_close($ch);
		if(isset($resp->error->error_user_msg))
			Session::flash('message',$resp->error->error_user_msg); 
		elseif(isset($resp->error->message))
			Session::flash('message',$resp->error->message);

		return view('social.adset',['campaigns'=>$resp->data]);
	}

	// For storing adset to fb via curl

	public function storeAdset(Request $request)
	{

		$request->validate([
			'name' => 'required',
			'destination_type' => 'required',
			'status' =>'required',
			'campaign_id'=>'required',
			'start_time'=>'required',
			'end_time'=>'required',
			'billing_event'=>'required',
			'bid_amount'=>'required',
		]);

		$data['name']=$request->input('name');
		$data['destination_type']=$request->input('destination_type');
		$data['campaign_id']=$request->input('campaign_id');
		$data['billing_event']=$request->input('billing_event');
		$data['start_time']=strtotime($request->input('start_time'));
		// $data['OPTIMIZATION_GOAL'] ='REACH';
		$data['end_time']=strtotime($request->input('end_time'));
		$data['targeting']=json_encode(array('geo_locations'=>array('countries' => array('US'))));
		if($request->has('daily_budget'))
			$data['daily_budget']=$request->input('daily_budget');

		$data['status']=$request->input('status');




		if($request->has('bid_amount'))
			$data['bid_amount']=$request->input('bid_amount');



		// Storing to fb via curl

		try{
			$data['access_token']=$this->user_access_token;



			$url="https://graph.facebook.com/v3.2/".$this->ad_acc_id.'/adsets';


			// Call to Graph api here
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


			$resp = curl_exec($curl);
			$resp = json_decode($resp);


			curl_close($curl);
			if(isset($resp->error->error_user_msg))
				Session::flash('message',$resp->error->error_user_msg); 
			elseif(isset($resp->error->message))
				Session::flash('message',$resp->error->message); 
			else
				Session::flash('message',"Adset created  successfully"); 


			return redirect()->route('social.ad.adset.create');
		}
		catch(Exception $e)
		{
			Session::flash('message',$e); 
			return redirect()->route('social.ad.adset.create');
		}

	}


	// for creating Ad
	public function createAd()
	{



		$query="https://graph.facebook.com/v3.2/".$this->ad_acc_id."/?fields=adsets{name,id},adcreatives{id,name}&limit=100&access_token=".$this->user_access_token."";


			// Call to Graph api here
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$query);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, 0);


		$resp = curl_exec($ch);
		$resp = json_decode($resp);


		curl_close($ch);
		if(isset($resp->error->message))
			Session::flash('message',$resp->error->message); 

		return view('social.ad',['adsets'=>$resp->adsets->data,'adcreatives'=>$resp->adcreatives->data]);
	}


	// For storing campaign to fb via curl

	public function storeAd(Request $request)
	{

		$request->validate([
			'name' => 'required',
			'adset_id' => 'required',
			'adcreative_id'=>'required',
			'status' =>'required',
		]);

		$data['name']=$request->input('name');
		$data['adset_id']=$request->input('adset_id');
		$data['creative']=json_encode(['creative_id'=>$request->input('adcreative_id')]);

		$data['status']=$request->input('status');

		// Storing to fb via curl

		try{
			$data['access_token']=$this->user_access_token;



			$url="https://graph.facebook.com/v3.2/".$this->ad_acc_id.'/ads';


			// Call to Graph api here
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


			$resp = curl_exec($curl);
			$resp = json_decode($resp);


			curl_close($curl);

			if(isset($resp->error->error_user_msg))
				Session::flash('message',$resp->error->error_user_msg); 
			elseif(isset($resp->error->message))
				Session::flash('message',$resp->error->error_user_msg); 
			else
				Session::flash('message',"Adset created  successfully"); 


			return redirect()->route('social.ad.create');
		}
		catch(Exception $e)
		{
			Session::flash('message',$e); 
			return redirect()->route('social.ad.create');
		}

	}


}


