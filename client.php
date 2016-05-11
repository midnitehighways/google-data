<?php
session_start();

/************************************************
		Setting client values and scopes
 ************************************************/
$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->addScope("https://www.googleapis.com/auth/drive");
$client->addScope("https://www.googleapis.com/auth/youtube");

// set redirect URI fitting both fetchandshow.herokuapp.com and localhost
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];  
$client->setRedirectUri($redirect_uri);

// $client->setAccessType('offline');
// if($client->isAccessTokenExpired()) {         // to avoid "token expiration" issue

/************************************************
  Create services for Drive and Youtube
 ************************************************/
$yt_service = new Google_Service_YouTube($client);
$dr_service = new Google_Service_Drive($client);


/************************************************
  Auth management: exchange token
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

 /************************************************
  Spreadsheets client
 ************************************************/
  
$objClientAuth  = new Google_Client ();
$objClientAuth -> setApplicationName (CLIENT_APP_NAME_W);
$objClientAuth -> setClientId (CLIENT_ID_W);
$objClientAuth -> setAssertionCredentials (new Google_Auth_AssertionCredentials (
    CLIENT_EMAIL_W, 
    array('https://spreadsheets.google.com/feeds','https://docs.google.com/feeds'), 
    file_get_contents (CLIENT_KEY_PATH_W), 
    CLIENT_KEY_PW_W
));
// $objClientAuth->setAuthConfigFile('service_secrets.json');
$objClientAuth->getAuth()->refreshTokenWithAssertion();
$objToken  = json_decode($objClientAuth->getAccessToken());
$accessToken = $objToken->access_token;
 
 
/**
 * Initialize the service request factory
 */ 
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
 
$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

?>