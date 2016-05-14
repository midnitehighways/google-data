<?php

/**
 * Display fetched data in spreadsheet
 * @param {array} $result_array - contains data fetched from Drive
 * @param {string} $table_header_1 && $table_header_2 - headers for a worksheet
 * @param {integer} $column_position - OX-position (left-upper corner) for a table
 * @var {array} $result_array - contains the number of each value in fetched $data_array
 */
function display_drive_data($data_array, $table_header_1, $table_header_2, $column_position) {
    
    // get the number of occurences for each element and sort results
    $result_array = array_count_values($data_array);
    ksort($result_array);
    
    $worksheet = provide_clear_worksheet();
    
    // set headers for a table
    $cellFeed = $worksheet->getCellFeed();
    $cellFeed->editCell(1, $column_position, $table_header_1);  
    $cellFeed->editCell(1, $column_position+1, $table_header_2);

    // display results in the spreadsheet, row by row
    $listFeed = $worksheet->getListFeed();
    foreach ($result_array as $key=>$value) {
        $row = array($table_header_1=>$key, $table_header_2=>$value); 
        $listFeed->insert($row);
    }
}

/**
 * Get given spreadsheet and worksheet and recreate worksheet: 
 * a way to clear its contents completely
 * @return {object} $worksheet - Data-sheet of report-spreadsheet in our case
 */
function provide_clear_worksheet(){
    
    // get spreadsheet by title
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle(SPREADSHEET_TITLE);

    // get particular worksheet of the selected spreadsheet 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);

    // recreate spreadsheet
    $worksheet->delete();
    $spreadsheet->addWorksheet(WORKSHEET_TITLE, 25, 15);
    $worksheetFeed = $spreadsheet->getWorksheets();

    return $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);
}

/**
 * Retrieve the 'Gid' of the needed worksheet.
 * This function is called when showing spreadsheet in <iframe>.
 * Thus, the needed worksheet is opened by default
 * @return {string} $worksheet's gid
 */
function get_data_worksheet_id(){
    
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle(SPREADSHEET_TITLE);

    // get particular worksheet of the selected spreadsheet 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);
    return $worksheet->getGid();//->getWorksheetId();
}

/*********************************
** Google Analytics (TESTING MODE!!!!)
**********************************/

/**
 * Get the user's first view (profile) ID
 * @param {reference} &$analytics
 * @return {string} $items[0]->getId()
 */
function getFirstProfileId(&$analytics) {
  
    // retrieve list of accounts
    $accounts = $analytics->management_accounts->listManagementAccounts();

    if (count($accounts->getItems()) > 0) {
        $items = $accounts->getItems();
        $firstAccountId = $items[0]->getId();

        // get list of properties
        $properties = $analytics->management_webproperties
            ->listManagementWebproperties($firstAccountId);

        if (count($properties->getItems()) > 0) {
            $items = $properties->getItems();
            $firstPropertyId = $items[0]->getId();

            // get list of views (profiles)
            $profiles = $analytics->management_profiles
                ->listManagementProfiles($firstAccountId, $firstPropertyId);

        if (count($profiles->getItems()) > 0) {
            $items = $profiles->getItems();
            // return the first view (profile) ID
            return $items[0]->getId();

          } else {
                throw new Exception('No views (profiles) found for this user.');
          }
        } else {
            throw new Exception('No properties found for this user.');
        }
  } else {
        throw new Exception('No accounts found for this user.');
  }
}
 
/**
 * Query API for the number of sessions
 * for the last seven days
 * @return {object} $a_results
 */
function getResults(&$analytics, $profileId) {
    
    $a_results = $analytics->data_ga->get(
        'ga:' . $profileId,
        '7daysAgo',
        'today',
        'ga:sessions');
    return $a_results;
}
/**
 * Parse response from API and display results
 * @param {reference} &$results
 */ 
function printResults(&$results) {
    if (count($results->getRows()) > 0) {

        // get the profile name
        $profileName = $results->getProfileInfo()->getProfileName();

        // get the entry for the first entry in the first row
        $rows = $results->getRows();
        $sessions = $rows[0][0];

        // display results
        //print "<p>First view (profile) found: $profileName</p>";
        print "<p>Info from Google Analytics for $profileName</p>
                <p>Total sessions for the last week: $sessions</p>";
    }
    else {
                print "<p>No Google Analytics account detected</p>";
    }
}



/*********************************
** HTML functions
**********************************/

/**
 * @param (string) $title - both site title and the main header
 * @var (string) $html
 * @return {string} $html
 */
function html_header($title)
{
    $html = "";
    $html .= "<!doctype html>
    <html>
    <head>
        <title>" . $title . "</title>
        <link href='style.css' rel='stylesheet' type='text/css' />
        <!--<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js'></script>-->
    </head>
    <body>
    <header><div>" . $title . "</div></header>
            <div class='left'>";
    return $html;
}

/**
 * start page - before authenticating
 * @param (string) $authUrl - previously created URL for authentification 
 * @return {string} $html
 */

function html_start($authUrl)
{
    $html = "";
    $html .= '<br><br><br><br><p>Welcome! This app needs your permission to get access to your Google Drive and Youtube accounts</p>
    <p>Please click "Start" to continue</p><br><br>
    <a class="my-button" href="' . $authUrl . '">Start</a> <br />';
    return $html;
}

/**
 * .left-div - form and three other buttons
 * @param (string) $username - $dr_service->about->get()->getName()
 * @return {string} $html
 */

function html_form($username)
{
    $html = "";
    $html .= '<a class="my-button logout" href="/?logout">Log out</a><br /><br />
        <span class="username">Hi, ' . $username . '</span>
        
        <p>Display and systematize data from Google Drive</p>
        <form method="POST">    
            <div class="drive">
                <img src="img/drive.ico">
                <span class=""> Consider trashed files <input type="checkbox" name="trashed" checked /></span><br/><br/>
                <input type="submit" class="my-button" value="File types" name="type_of_file"><br/><br/>
                <input type="submit" class="my-button" value="Time created" name="year_created"><br/><br/>
                <input type="submit" class="my-button" value="List Drive files" name="list_files">
            </div>

            <br/><p>Retrive some data from YouTube</p>
            <div class="youtube">
                <img src="img/youtube.png">
                <input type="submit" class="my-button" value="Time liked" name="year_liked"><br/><br/>
                <input type="submit" class="my-button" value="List liked videos" name="list_videos"><br/>
            </div>

            <a target="_blank" 
            href="' . SPREADSHEET_URL . 'edit#gid=' . get_data_worksheet_id() . '">
            <img src="img/fullscreen.gif"></a>&nbsp&nbsp
            <a href="' . SPREADSHEET_URL . 'export?format=xlsx"> <img src="img/save.png"></a>&nbsp&nbsp
            <a name="clear" href="?clear">  <img src="img/clear.png"></a>
        </form>';
    return $html;
}

/**
 * iframe (right part) and footer
 * @return {string} $html
 */

function html_closing_part()
{
    $html = "";
    $html .= '</div>
            <iframe class="spreadsheet" src="' . SPREADSHEET_URL . 'edit#gid='.get_data_worksheet_id().'"></iframe>
            <div class="footer">
                &#169; 2016 
                <a target="_blank" href="https://www.linkedin.com/in/alexandruoat">Alexandru Oat</a> | This project on 
                <a target="_blank" href="https://github.com/midnitehighways/google-data">GitHub</a> 
            </div>
        </body>
    </html>';
    return $html;
}


/*
**************************************
    OLD AND UNUSED FUNCTIONS BELOW
    not removed 'just in case'
**************************************
*/ 


function get_drive_filetypes($result_array) {
    ksort($result_array);
    // get spreadsheet by title
    $spreadsheetTitle = 'report';
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle($spreadsheetTitle);

    // get particular worksheet of the selected spreadsheet
    $worksheetTitle = 'Sheet1'; 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle($worksheetTitle);

    // set headers for a table
    $cellFeed = $worksheet->getCellFeed();
    $cellFeed->editCell(1, 1, "filetype");  // 1st row, 1st column
    $cellFeed->editCell(1, 2, "number");    // 1st row, 2nd column

    // display keys and values of fetched array in the spreadshit
    $listFeed = $worksheet->getListFeed();
    foreach ($result_array as $file_type=>$file_number) {
        $row = array('filetype'=>$file_type, 'number'=>$file_number); 
        $listFeed->insert($row);
    }
}

function get_drive_created_dates($result_array) {
    ksort($result_array);
    // get spreadsheet by title
    $spreadsheetTitle = 'report';
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle($spreadsheetTitle);

    // get particular worksheet of the selected spreadsheet
    $worksheetTitle = 'Drive'; 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle($worksheetTitle);

    $worksheet->delete();

    $spreadsheet->addWorksheet($worksheetTitle, 20, 10);
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle($worksheetTitle);

    // set headers for a table
    $cellFeed = $worksheet->getCellFeed();
    $cellFeed->editCell(1, 4, "year");  // 1st row, 4th column
    $cellFeed->editCell(1, 5, "filenumber");    // 1st row, 5th column

    // display keys and values of fetched array in the spreadshit
    $listFeed = $worksheet->getListFeed();

    // foreach ($listFeed->getEntries() as $entry) {
    //     $values['year'] = 'n';
    //     $values['filenumber'] = 's';
    //     $entry->delete();
    // }
    foreach ($result_array as $file_type=>$file_number) {
        $row = array('year'=>$file_type, 'filenumber'=>$file_number); 
        $listFeed->insert($row);
    }

}

?>