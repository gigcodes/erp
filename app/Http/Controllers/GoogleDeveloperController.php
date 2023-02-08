<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Client;
use Google\Client;
// use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\RefreshToken;

// use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
session_start();  

class GoogleDeveloperController extends Controller
{
    public static function getDeveloperApianr()
    {
        
    // ------------------------------
//          $base_url = 'https://accounts.google.com/o/oauth2/auth';
//         $http = new Client(['base_url' => $base_url]);
        
// $config = [
//     'username' => 'naveenprasanth@gmail.com',
//     'password' => '7kgHRygZ8xRjYbZ',
// ];
// // $oauth2Client = new Client(['base_url' => $base_url]);
//  $refreshToken = new RefreshToken($http, $config);
//     $response = $http->post('https://oauth2.googleapis.com/token', [
//         'form_params' => [
//             'grant_type' => 'authorization_code',
//              'client_id' => '898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com',
//     'client_secret'=>'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',
//     'redirect_uri'=>'https://www.getpostman.com/oauth2/callback',
//     'scope' => 'https://www.googleapis.com/auth/playdeveloperreporting',
//             'auth_url'=>'https://accounts.google.com/o/oauth2/auth',
//             'code' => $refreshToken ,
//         ],
//     ]);
 
    // return json_decode((string) $response->getBody(), true);
        // --------------------------------------------------
//         $base_url = 'https://accounts.google.com/o/oauth2/auth';

// $oauth2Client = new Client(['base_url' => $base_url]);

// $config = [
//     'username' => 'naveenprasanth@gmail.com',
//     'password' => '7kgHRygZ8xRjYbZ',
//     'client_id' => '898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com',
//     'client_secret'=>'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',
//     'redirect_uri'=>'https://www.getpostman.com/oauth2/callback',
//     'scope' => 'https://www.googleapis.com/auth/playdeveloperreporting',
// ];

// $token = new AuthorizationCode($oauth2Client, $config);
// $refreshToken = new RefreshToken($oauth2Client, $config);

// $oauth2 = new Oauth2Subscriber($token, $refreshToken);

// $client = new Client([
//     'defaults' => [
//         'auth' => 'oauth2',
//         'subscribers' => [$oauth2],
//     ],
// ]);

//         $client = new Client();
//         $client->setApplicationName("Client_Library_Examples");
// // $client->setScopes(['https://www.googleapis.com/auth/playdeveloperreporting']);
// $client->setAuthConfig('client_cred.json');
// // $user_to_impersonate = 'naveenprasath@gmail.com';
// // $client->setSubject($user_to_impersonate);
// // $scopes = [ Google_Service_Books::BOOKS ];
// $client->addScope('https://www.googleapis.com/auth/playdeveloperreporting');
// // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// $redirect_uri='https://www.getpostman.com/oauth2/callback';
// // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// $client->setRedirectUri($redirect_uri);
//  // $playdeveloperreportingService = new Google\Service\Playdeveloperreporting();
//  // $crashrate = $playdeveloperreportingService->vitals_crashrate;
//  // $crashrate1=$crashrate->get("com.santhilag.starmarket");
// // $user_to_impersonate = 'naveenprasath@gmail.com';
// // $client->setSubject($user_to_impersonate);
// $token=$redirect_uri;
// if (isset($_GET['code'])) {
//     $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
// }

// if ($client->getAuth()->isAccessTokenExpired()) {
//     $client->getAuth()->refreshTokenWithAssertion();
// }

$output="";
// if($request->ajax())
// {
$output2='';
$res="";
$client = new Client();
$client->setApplicationName("santhila-208405");
// $client->setAuthConfig('client_cred.json');
$client->setDeveloperKey("AIzaSyA7906By4DEb03gD5T6udRbNByyt5QRxkw");
  $client->setClientId('898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com');
    $client->setClientSecret('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');
    
    $client->setScopes(array('https://www.googleapis.com/auth/playdeveloperreporting'));
// $client->addScope('https://www.googleapis.com/auth/playdeveloperreporting');
// $redirect = 'http://luxaryerp.com:8000/google/developer-api/crash';


$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirect_uri);

// header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
 if (isset($_GET['code'])) {
session_unset();
        $client->authenticate($_GET['code']);  
        $_SESSION['token'] = $client->getAccessToken();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        $output=$client->getAccessToken();
    }
    // if(!isset($_GET['search']))
    // {
    //     session_unset();
    // }
     if (!$client->getAccessToken() && !isset($_SESSION['token'])) {
        $authUrl = $client->createAuthUrl();
       $output="connect";
       $output2=$authUrl;
        } 
        else
        {
            $at=(string)$_SESSION['token']["access_token"];
            // print_r($_SESSION['token']["access_token"] );
            $client = new Client();
// $request2 = new Request('GET', 'https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.santhilag.starmarket/crashRateMetricSet?access_token='.$at );
// print($_GET['search']);
            if(isset($_GET['search']))
            {
$res =  Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.$_GET['search'].'/anrRateMetricSet?access_token='.$at);
}
else{
    $res="enter the app name";
}
// print_r($res);
// com.santhilag.starmarket
        }
// $output=$redirect_uri;
// $products=DB::table('products')->where('title','LIKE','%'.$request->search."%")->get();
// if($products)
// {
// foreach ($products as $key => $product) {
// $output.='<tr>'.
// '<td>'.$product->id.'</td>'.
// '<td>'.$product->title.'</td>'.
// '<td>'.$product->description.'</td>'.
// '<td>'.$product->price.'</td>'.
// '</tr>';
// }
// return Response($output);




// }


// 
         return view('google.developer-api.anr', compact('output','output2', 'res'));
    }
    
    
// public function indexcrash()
// {
//     $output="";
//     $output2="";
//     $client = new Client();

    
//       return view('google.developer-api.crash', compact('output','output2'));
// }
    
    public function getDeveloperApicrash()
    {
        // ------------------------------
//          $base_url = 'https://accounts.google.com/o/oauth2/auth';
//         $http = new Client(['base_url' => $base_url]);
        
// $config = [
//     'username' => 'naveenprasanth@gmail.com',
//     'password' => '7kgHRygZ8xRjYbZ',
// ];
// // $oauth2Client = new Client(['base_url' => $base_url]);
//  $refreshToken = new RefreshToken($http, $config);
//     $response = $http->post('https://oauth2.googleapis.com/token', [
//         'form_params' => [
//             'grant_type' => 'authorization_code',
//              'client_id' => '898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com',
//     'client_secret'=>'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',
//     'redirect_uri'=>'https://www.getpostman.com/oauth2/callback',
//     'scope' => 'https://www.googleapis.com/auth/playdeveloperreporting',
//             'auth_url'=>'https://accounts.google.com/o/oauth2/auth',
//             'code' => $refreshToken ,
//         ],
//     ]);
 
    // return json_decode((string) $response->getBody(), true);
        // --------------------------------------------------
//         $base_url = 'https://accounts.google.com/o/oauth2/auth';

// $oauth2Client = new Client(['base_url' => $base_url]);

// $config = [
//     'username' => 'naveenprasanth@gmail.com',
//     'password' => '7kgHRygZ8xRjYbZ',
//     'client_id' => '898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com',
//     'client_secret'=>'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',
//     'redirect_uri'=>'https://www.getpostman.com/oauth2/callback',
//     'scope' => 'https://www.googleapis.com/auth/playdeveloperreporting',
// ];

// $token = new AuthorizationCode($oauth2Client, $config);
// $refreshToken = new RefreshToken($oauth2Client, $config);

// $oauth2 = new Oauth2Subscriber($token, $refreshToken);

// $client = new Client([
//     'defaults' => [
//         'auth' => 'oauth2',
//         'subscribers' => [$oauth2],
//     ],
// ]);

//         $client = new Client();
//         $client->setApplicationName("Client_Library_Examples");
// // $client->setScopes(['https://www.googleapis.com/auth/playdeveloperreporting']);
// $client->setAuthConfig('client_cred.json');
// // $user_to_impersonate = 'naveenprasath@gmail.com';
// // $client->setSubject($user_to_impersonate);
// // $scopes = [ Google_Service_Books::BOOKS ];
// $client->addScope('https://www.googleapis.com/auth/playdeveloperreporting');
// // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// $redirect_uri='https://www.getpostman.com/oauth2/callback';
// // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// $client->setRedirectUri($redirect_uri);
//  // $playdeveloperreportingService = new Google\Service\Playdeveloperreporting();
//  // $crashrate = $playdeveloperreportingService->vitals_crashrate;
//  // $crashrate1=$crashrate->get("com.santhilag.starmarket");
// // $user_to_impersonate = 'naveenprasath@gmail.com';
// // $client->setSubject($user_to_impersonate);
// $token=$redirect_uri;
// if (isset($_GET['code'])) {
//     $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
// }

// if ($client->getAuth()->isAccessTokenExpired()) {
//     $client->getAuth()->refreshTokenWithAssertion();
// }

$output="";
// if($request->ajax())
// {
$output2='';
$res="";
$client = new Client();
$client->setApplicationName("santhila-208405");
// $client->setAuthConfig('client_cred.json');
$client->setDeveloperKey("AIzaSyA7906By4DEb03gD5T6udRbNByyt5QRxkw");
  $client->setClientId('898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com');
    $client->setClientSecret('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');
    
    $client->setScopes(array('https://www.googleapis.com/auth/playdeveloperreporting'));
// $client->addScope('https://www.googleapis.com/auth/playdeveloperreporting');
// $redirect = 'http://luxaryerp.com:8000/google/developer-api/crash';


$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirect_uri);

// header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
 if (isset($_GET['code'])) {
session_unset();
        $client->authenticate($_GET['code']);  
        $_SESSION['token'] = $client->getAccessToken();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        $output=$client->getAccessToken();
    }
    // if(!isset($_GET['search']))
    // {
    //     session_unset();
    // }
     if (!$client->getAccessToken() && !isset($_SESSION['token'])) {
        $authUrl = $client->createAuthUrl();
       $output="connect";
       $output2=$authUrl;
        } 
        else
        {
            $at=(string)$_SESSION['token']["access_token"];
            // print_r($_SESSION['token']["access_token"] );
            $client = new Client();
// $request2 = new Request('GET', 'https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.santhilag.starmarket/crashRateMetricSet?access_token='.$at );
// print($_GET['search']);
             if(isset($_GET['search']))
             {
$res =  Http::get('https://playdeveloperreporting.googleapis.com/v1beta1/apps/'.$_GET['search'].'/crashRateMetricSet?access_token='.$at);
// print_r($res);
// com.santhilag.starmarket
        }

else{
    $res="enter the app name";
}
}
// $output=$redirect_uri;
// $products=DB::table('products')->where('title','LIKE','%'.$request->search."%")->get();
// if($products)
// {
// foreach ($products as $key => $product) {
// $output.='<tr>'.
// '<td>'.$product->id.'</td>'.
// '<td>'.$product->title.'</td>'.
// '<td>'.$product->description.'</td>'.
// '<td>'.$product->price.'</td>'.
// '</tr>';
// }
// return Response($output);




// }


// 
         return view('google.developer-api.crash', compact('output','output2', 'res'));
    }
}
