<?php
//include 'base.php';
session_start();
/************************************************
			secret values + redirect_uri
 ************************************************/
 $client_id = '958391346195-2io8faetghst4eumgdik32q3rv5u9jh4.apps.googleusercontent.com';
 $client_secret = 'BodELQQQQ_nmY53e4aEa3s9q';
 $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];    // fits both fetchandshow.herokuapp.com and localhost

/************************************************
		setting client values and scopes
 ************************************************/
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$client->addScope("https://www.googleapis.com/auth/youtube");

// $client->setAccessType('offline'); ////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ??????????????????????????? added 13.57

// if($client->isAccessTokenExpired()) {         // to avoid "token expiration" issue

//     $authUrl = $client->createAuthUrl();
//     header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

// }
/************************************************
  create services for Drive and Youtube
 ************************************************/
$yt_service = new Google_Service_YouTube($client);
$dr_service = new Google_Service_Drive($client);


/************************************************

 ************************************************/
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}
?>