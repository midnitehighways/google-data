<?php


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

function html_start($authUrl)
{
    $html = "";
    $html .= '<br><br><br><br><p>Welcome! This app needs your permission to get access to your Google Drive and Youtube accounts</p>
    <p>Please click "Start" to continue</p><br><br>
    <a class="my-button" href="' . $authUrl . '">Start</a> <br />';
    return $html;
}

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
            <input type="submit" class="my-button" value="File types" name="submit"><br/><br/>
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
            href="https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/edit#gid='
            .get_data_worksheet_id().'"><img src="img/fullscreen.gif"></a>&nbsp&nbsp
            <a href="https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/export?format=xlsx">
            <img src="img/save.png"></a>&nbsp&nbsp
            <a name="clear" href="?clear">  <img src="img/clear.png"></a>
        </form>';
    return $html;
}


function provide_clear_worksheet(){
    // get spreadsheet by title
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle(SPREADSHEET_TITLE);

    // get particular worksheet of the selected spreadsheet 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);

    $worksheet->delete();

    $spreadsheet->addWorksheet(WORKSHEET_TITLE, 15, 9);
    $worksheetFeed = $spreadsheet->getWorksheets();

    return $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);
}
/**
 * Display fetched data in spreadsheet
 * @param array $result_array - contains data fetched from Drive
 * @param array $table_header_1 - header for a worksheet
 * @param array $column_position - OX-position (left-upper corner) for a table
 * @return nothing so far
 */
function display_drive_data($result_array, $table_header_1, $table_header_2, $column_position) {
    ksort($result_array);
    $worksheet = provide_clear_worksheet();
    // set headers for a table
    $cellFeed = $worksheet->getCellFeed();
    $cellFeed->editCell(1, $column_position, $table_header_1);  
    $cellFeed->editCell(1, $column_position+1, $table_header_2);

    // display keys and values of fetched array in the spreadshit
    $listFeed = $worksheet->getListFeed();

    foreach ($result_array as $key=>$value) {
        $row = array($table_header_1=>$key, $table_header_2=>$value); 
        $listFeed->insert($row);
    }
}

function get_data_worksheet_id(){
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle(SPREADSHEET_TITLE);

    // get particular worksheet of the selected spreadsheet 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle(WORKSHEET_TITLE);
    return $worksheet->getGid();//->getWorksheetId();
}







/*
**************************************
    OLD AND NOT USED FUNCTIONS BELOW
    didn't remove 'just in case'
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