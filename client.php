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

//$client->refreshToken($client->getRefreshToken());
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



// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/ServiceRequestInterface.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/DefaultServiceRequest.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/Exception.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/UnauthorizedException.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/ServiceRequestFactory.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/SpreadsheetService.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/SpreadsheetFeed.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/Spreadsheet.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/WorksheetFeed.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/Worksheet.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/ListFeed.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/ListEntry.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/CellFeed.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/CellEntry.php';
// require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/Util.php';
 
 
/**
 * AUTHENTICATE
 *
 */
// SERVICE ACCOUNT
const CLIENT_APP_NAME = 'mySpreadsheets';
const CLIENT_ID       = '110200043135369381803';
const CLIENT_EMAIL    = 'newphp-11@appspot.gserviceaccount.com';
const CLIENT_KEY_PATH = 'newphp-9c49ef7b78fe.p12'; // PATH_TO_KEY = where you keep your key file
const CLIENT_KEY_PW   = 'notasecret';
 
$objClientAuth  = new Google_Client ();
$objClientAuth -> setApplicationName (CLIENT_APP_NAME);
$objClientAuth -> setClientId (CLIENT_ID);
$objClientAuth -> setAssertionCredentials (new Google_Auth_AssertionCredentials (
    CLIENT_EMAIL, 
    array('https://spreadsheets.google.com/feeds','https://docs.google.com/feeds'), 
    file_get_contents (CLIENT_KEY_PATH), 
    CLIENT_KEY_PW
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