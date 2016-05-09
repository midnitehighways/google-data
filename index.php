<?php
include_once 'base.php';
session_start();

//require_once 'google-api-php-client/src/Google/autoload.php';
require 'vendor/autoload.php';
require_once 'client.php';

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

echo pageHeader("Fetch Google Data and Show Stats");
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
<iframe class="spreadsheet" src="https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/edit#gid=0"></iframe>


<?php 
if (isset($authUrl)) {
    echo "<a href='" . $authUrl . "'>Connect</a> <br /> \n";
} else {
    echo "<a href='/?logout'>Log out</a>";
    echo '<form method="POST">
            Trashed files <input type="checkbox" name="trashed" value="Yes" />
            <input type="submit" name="submit">
        </form>';

    //echo "<h3>Results Of Drive List:</h3>";
    $drive_filetypes = array();
    var_dump($_POST);
    // function test_trashed($var) {

    // }
    foreach ($dr_results as $item) {
        if((isset($_POST['trashed'])) || (!$item->labels->trashed)) {       // either 'show trashed' checked or file isn't trashed
            echo $item->createdDate, "<br /> \n";
            $info = new SplFileInfo($item->title);           // get file extension
            //array_push($my, $item->title);
            if($info->getExtension()) {
              array_push($drive_filetypes, $info->getExtension());
              //echo $info->getExtension(), "<br /> \n";
            }
            else {                                           // no extension
              array_push($drive_filetypes, "none");
              //echo "none <br /> \n";
            }
        }
    }
  //echo "hey",$dr_results->title;
  //echo "hiii",$my[6];
  //print_r(array_count_values($my));
  $my_2 = array();
  $my_2 = array_count_values($drive_filetypes);
  //echo "heyy",$my_2[0];
  echo "<h3>Results Of YouTube Likes:</h3>";
  foreach ($yt_results as $item) {
    echo $item['snippet']['title'], "<br /> \n";
  }
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
// These settings are found on google developer console
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

require_once 'functions.php';
if(isset($_POST["submit"])) {
    getDriveFiletypes($my_2);
}
/**
 * Get spreadsheet by title
 */


// -------
// $spreadsheetTitle = 'report';
// $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
// $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
// $spreadsheet = $spreadsheetFeed->getByTitle($spreadsheetTitle);
 
 
/**
 * Add new worksheet to the spreadsheet
 */
//$worksheetTitle = 'New Worksheet';
//$spreadsheet->addWorksheet($worksheetTitle, 50, 20); // 50 rows & 20 columns
 
 
/**
 * Get particular worksheet of the selected spreadsheet
 */





// -------
// $worksheetTitle = 'Sheet1'; 
// $worksheetFeed = $spreadsheet->getWorksheets();
// $worksheet = $worksheetFeed->getByTitle($worksheetTitle);
 



 
/**
 * Delete a particular worksheet from the spreadsheet
 * I have commented out the worksheet delete code 
 * but it works well
 */
//$worksheetTitle = 'YOUR-WORKSHEET-TITLE'; 
//$worksheet = $worksheetFeed->getByTitle($worksheetTitle);
//$worksheet->delete();
 
 
/** 
 * Add/update headers of worksheet
 */




// -------
// $cellFeed = $worksheet->getCellFeed();
// $cellFeed->editCell(1, 1, "filetype"); // 1st row, 1st column
// $cellFeed->editCell(1, 2, "number"); // 1st row, 2nd column
 




 
/** 
 * Insert row entries
 * Supposing, that there are two headers 'name' and 'age'
 */
//$row = array('filetype'=>'John', 'number'=>25);

 
 
/**
 * Get row lists of worksheet
 */




// -------
// foreach ($my_2 as $file_type=>$file_number) {

//   $listFeed = $worksheet->getListFeed();
//   $row = array('filetype'=>$file_type, 'number'=>$file_number); 
//   $listFeed->insert($row);
//}

/**
 * Print row lists
 */





// -------

// foreach ($listFeed->getEntries() as $entries) {
//   print_r($entries->getValues()); 
// }
 








 
/**
 * Update row entries
 * Supposing, that there are two headers 'name' and 'age'
 */
/*
$entries = $listFeed->getEntries();
$listEntry = $entries[0]; // 0 = 1st row (editing value of first row)
//$values = $listEntry->getValues(); 
$values['filetype'] = 'Bob';
$values['number'] = '45';
$listEntry->update($values);
*/


// $objPHPExcel = new PHPExcel();
// $objWorksheet = $objPHPExcel->getActiveSheet();
// $objWorksheet->fromArray($my_2, null, 'A1'
//   // array(
//   //   array('type', 'number'),
//   //   array('Q1',   12),
//   //   array('Q2',   56),
//   //   array('Q3',   52),
//   //   array('Q4',   30),
//   // )
// );
// //  Set the Labels for each data series we want to plot
// //    Datatype
// //    Cell reference for data
// //    Format Code
// //    Number of datapoints in series
// //    Data values
// //    Data Marker
// $dataSeriesLabels = array(
//   new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$1', NULL, 1), //  2010
//   //new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1), //  2011
//   //new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', NULL, 1), //  2012
// );
// //  Set the X-Axis Labels
// //    Datatype
// //    Cell reference for data
// //    Format Code
// //    Number of datapoints in series
// //    Data values
// //    Data Marker
// $xAxisTickValues = array(
//   new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$5', NULL, 4),  //  Q1 to Q4
// );
// //  Set the Data values for each data series we want to plot
// //    Datatype
// //    Cell reference for data
// //    Format Code
// //    Number of datapoints in series
// //    Data values
// //    Data Marker
// $dataSeriesValues = array(
//   new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', NULL, 4),
//   //new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$5', NULL, 4),
//   //new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$5', NULL, 4),
// );
// //  Build the dataseries
// $series = new PHPExcel_Chart_DataSeries(
//   PHPExcel_Chart_DataSeries::TYPE_BARCHART,   // plotType
//   PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
//   range(0, count($dataSeriesValues)-1),     // plotOrder
//   $dataSeriesLabels,                // plotLabel
//   $xAxisTickValues,               // plotCategory
//   $dataSeriesValues               // plotValues
// );
// //  Set additional dataseries parameters
// //    Make it a horizontal bar rather than a vertical column graph
// $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);
// //  Set the series in the plot area
// $plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));
// //  Set the chart legend
// //$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
// $title = new PHPExcel_Chart_Title('Files in Google Drive by type (extension)');
// $yAxisLabel = new PHPExcel_Chart_Title('File number');
// //  Create the chart
// $chart = new PHPExcel_Chart(
//   'chart1',   // name
//   $title,     // title
//  //$legend,    // legend
//   null,
//   $plotArea,    // plotArea
//   true,     // plotVisibleOnly
//   0,        // displayBlanksAs
//   NULL,     // xAxisLabel
//   $yAxisLabel   // yAxisLabel
// );
// //  Set the position where the chart should appear in the worksheet
// $chart->setTopLeftPosition('A7');
// $chart->setBottomRightPosition('H20');
// //  Add the chart to the worksheet
// $objWorksheet->addChart($chart);
// // Save Excel 2007 file
// echo date('H:i:s') , " Write to Excel2007 format" , EOL;
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->setIncludeCharts(TRUE);
// //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
// $objWriter->save(str_replace(__FILE__,'indexx.xlsx',__FILE__));
// //$objWriter->save('php://output');
// echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// // Echo memory peak usage
// echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
// // Echo done
// echo date('H:i:s') , " Done writing file" , EOL;
// echo 'File has been created in ' , getcwd() , EOL;



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