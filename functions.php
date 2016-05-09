<?php
//session_start();

/**
 * Display fetched data in spreadsheet
 * @param array $result_array - contains data fetched from Drive
 * @return nothing so far
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

    $spreadsheet->addWorksheet('Drive', 20, 10);
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle($worksheetTitle);

    // $worksheetTitle = 'Sheet1'; 
    // $worksheetFeed = $spreadsheet->getWorksheets();
    // $worksheet = $worksheetFeed->getByTitle($worksheetTitle);
    // $worksheet->delete();



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

function get_drive_worksheet_id(){
    $spreadsheetTitle = 'report';
    $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
    $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
    $spreadsheet = $spreadsheetFeed->getByTitle($spreadsheetTitle);

    // get particular worksheet of the selected spreadsheet
    $worksheetTitle = 'Drive'; 
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle($worksheetTitle);
    return $worksheet->getGid();//->getWorksheetId();
}
?>