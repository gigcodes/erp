<?php
$url = "http://erp.sololuxury.co.in/whatsapp/incoming";
$content = $_POST['data'];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
        array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$params = [
  'request' => $content,
  'response' => $response,
  'status' => $status
];
file_put_contents(__DIR__."/log.txt", json_encode($params));

curl_close($curl);
