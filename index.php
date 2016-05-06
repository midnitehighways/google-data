<?php
include_once 'base.php';
session_start();

//require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');
require_once 'google-api-php-client/src/Google/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Helsinki');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
date_default_timezone_set('Europe/Helsinki');
require_once 'Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'Classes/PHPExcel/Writer/Excel2007.php';


$objPHPExcel = new PHPExcel();
$objWorksheet = $objPHPExcel->getActiveSheet();
$objWorksheet->fromArray(
	array(
		array('',	2010,	2011,	2012),
		array('Q1',   12,   15,		21),
		array('Q2',   56,   73,		86),
		array('Q3',   52,   61,		69),
		array('Q4',   30,   32,		0),
	)
);
//	Set the Labels for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesLabels = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', NULL, 1),	//	2010
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1),	//	2011
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', NULL, 1),	//	2012
);
//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$5', NULL, 4),	//	Q1 to Q4
);
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = array(
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', NULL, 4),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$5', NULL, 4),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$5', NULL, 4),
);
//	Build the dataseries
$series = new PHPExcel_Chart_DataSeries(
	PHPExcel_Chart_DataSeries::TYPE_AREACHART,				// plotType
	PHPExcel_Chart_DataSeries::GROUPING_PERCENT_STACKED,	// plotGrouping
	range(0, count($dataSeriesValues)-1),					// plotOrder
	$dataSeriesLabels,										// plotLabel
	$xAxisTickValues,										// plotCategory
	$dataSeriesValues										// plotValues
);
//	Set the series in the plot area
$plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));
//	Set the chart legend
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_TOPRIGHT, NULL, false);
$title = new PHPExcel_Chart_Title('Test %age-Stacked Area Chart');
$yAxisLabel = new PHPExcel_Chart_Title('Value ($k)');
//	Create the chart
$chart = new PHPExcel_Chart(
	'chart1',		// name
	$title,			// title
	$legend,		// legend
	$plotArea,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	NULL,			// xAxisLabel
	$yAxisLabel		// yAxisLabel
);
//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');
//	Add the chart to the worksheet
$objWorksheet->addChart($chart);
// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;




/************************************************
			secret values + redirect_uri
 ************************************************/
 $client_id = '958391346195-2io8faetghst4eumgdik32q3rv5u9jh4.apps.googleusercontent.com';
 $client_secret = 'BodELQQQQ_nmY53e4aEa3s9q';
 $redirect_uri = 'http://localhost:8080';

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

/************************************************
  If we're signed in, retrieve channels from YouTube
  and a list of files from Drive.
 ************************************************/
if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();

  $dr_results = $dr_service->files->listFiles(array());

  $yt_channels = $yt_service->channels->listChannels('contentDetails', array("mine" => true));
  $likePlaylist = $yt_channels[0]->contentDetails->relatedPlaylists->likes;
  $yt_results = $yt_service->playlistItems->listPlaylistItems(
      "snippet",
      array("playlistId" => $likePlaylist)
  );
}

echo pageHeader("User Query - Multiple APIs");
if (strpos($client_id, "googleusercontent") == false) {
  echo missingClientSecretsWarning();
  exit;
}
?>
<html>
<head>
	<title>Google API</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>



<?php 
if (isset($authUrl)) {
  echo "<a href='" . $authUrl . "'>Connect</a>";
} else {
  echo "<a href='/?logout'>Log out</a>";
  echo "<h3>Results Of Drive List:</h3>";
  $my = array();
  foreach ($dr_results as $item) {
    //echo $item->name, "<br /> \n";
    $info = new SplFileInfo($item->name);           // get file extension
    //array_push($my, $item->name);
    if($info->getExtension()) {
      array_push($my, $info->getExtension());
      echo $info->getExtension(), "<br /> \n";
    }
    else {                                           // no extension
      array_push($my, "none");
      echo "none <br /> \n";
    }
  }
  //echo "hey",$dr_results->name;
  echo "hiii",$my[6];
  print_r(array_count_values($my));
  echo "<h3>Results Of YouTube Likes:</h3>";
  foreach ($yt_results as $item) {
    echo $item['snippet']['title'], "<br /> \n";
  }
} 
//echo pageFooter(__FILE__);





// require_once 'google-api-php-client/src/Google/autoload.php';

//     session_start();

//     $client = new Google_Client();
//     $client->setAuthConfigFile('client_secrets.json');
//     $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

//     if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//         $client->setAccessToken($_SESSION['access_token']);
//         $drive_service = new Google_Service_Drive($client);
//         $files_list = $drive_service->files->listFiles(array())->getItems();
//         echo json_encode($files_list);
//     } else {
//         $redirect_uri = 'http://localhost/oauth2callback.php';
//         header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
//     }   














// require_once 'google-api-php-client/src/Google/autoload.php';
// require_once 'google-api-php-client/src/Google/Service/Drive.php';

// session_start();

// $client = new Google_Client();
// $client->setAuthConfigFile('client_secrets.json');
// $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
// // $client->setScopes(array('https://www.googleapis.com/auth/drive')); 

// // function retrieveAllFiles($service) {
// //   $result = array();
// //   $pageToken = NULL;

// //   do {
// //     try {
// //       $parameters = array();
// //       if ($pageToken) {
// //         $parameters['pageToken'] = $pageToken;
// //       }
// //       $files = $service->files->listFiles($parameters);

// //       $result = array_merge($result, $files->getItems());
// //       $pageToken = $files->getNextPageToken();
// //     } catch (Exception $e) {
// //       print "An error occurred: " . $e->getMessage();
// //       $pageToken = NULL;
// //     }
// //   } while ($pageToken);
// //   return $result;
// // }

// if (isset($_REQUEST['logout'])) {			// add '?logout' to URL string to log out
//   	unset($_SESSION['access_token']);
//  	echo "You've logged out. Bye!";
// }
// if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//   $client->setAccessToken($_SESSION['access_token']);
//   $drive_service = new Google_Service_Drive($client);
// //   $optParams = array(
// //   'pageSize' => 10,
// //   'fields' => "nextPageToken, files(id, name)"
// // );

// $results = $drive_service->files;//->listFiles(array());
// $aa = $results -> listFiles(array());
// //array_push($aa,'value');
// //$files_list = $aa -> items;
// // $files_list = retrieveAllFiles($drive_service);
//   // $files_list = $drive_service->files->listFiles(array())->getItems();
//   //$files_list = retrieveAllFiles($drive_service);
//   // $files_list = $drive_service->files->listFiles(array());//(array())->getItems();
//   //$files_list = $drive_service;
//   //echo json_encode($files_list);
// 	// $token_data = $client->verifyIdToken();
// 	// echo var_export($token_data);
// echo json_encode($client);
// } else {
//   $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
//   header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
// }
// session_destroy();

?>
</body>
</html>