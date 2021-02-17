<?php

$url = 'https://we.tl/t-o31lQSVkKz';


$WETRANSFER_API_URL = 'https://wetransfer.com/api/v4/transfers/';



if (strpos($url, 'https://we.tl/') !== false) {
    
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0"); // Necessary. The server checks for a valid User-Agent.
	curl_exec($ch);

	$response = curl_exec($ch);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches);
	curl_close($ch);

	if(isset($matches[1])){
		if(isset($matches[1][0])){
			$url = trim($matches[1][0]);
		}
	}

}

//replace https://wetransfer.com/downloads/ from url

$url = str_replace('https://wetransfer.com/downloads/', '', $url);

//making array from url

$dataArray = explode('/', $url);

if(count($dataArray) == 2){
	$securityhash = $dataArray[1];
	$transferId = $dataArray[0];
}elseif(count($dataArray) == 3){
	$securityhash = $dataArray[2];
	$recieptId = $dataArray[1];
	$transferId = $dataArray[0];
}else{
	die('Something is wrong with url');
}



$header = getCsrfFromWebsite();

//making post request to get the url
$data = array();
$data['intent'] = 'entire_transfer';
$data['security_hash'] = $securityhash;

$curlURL = $WETRANSFER_API_URL.$transferId.'/download'; 

  $cookie= "cookie.txt";
  $url='https://wetransfer.com/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIESESSION, true);
  curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/'.$cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/'.$cookie);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  if (curl_errno($ch)) die(curl_error($ch));

  $re = '/name="csrf-token" content="([^"]+)"/m';

	preg_match_all($re, $response, $matches, PREG_SET_ORDER, 0);

	if(count($matches) != 0){
		if(isset($matches[0])){
			if(isset($matches[0][1])){
				$token = $matches[0][1];
			}
		}
	}

  $headers[] = 'Content-Type: application/json';
  $headers[] = 'X-CSRF-Token:' .  $token;

  curl_setopt($ch, CURLOPT_URL, $curlURL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

  $real = curl_exec($ch);

  echo $real;
  die();





