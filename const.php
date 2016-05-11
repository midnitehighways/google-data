<?php 

date_default_timezone_set('Europe/Helsinki');

/**
 ****************** SPREADSHEET *****************************
 */

// ACTIVE SPREADSHEET AND WORKSHEET (stay constant)
const SPREADSHEET_TITLE = "report";
const WORKSHEET_TITLE 	= "Data";
const SPREADSHEET_URL 	= "https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/";

// SERVICE ACCOUNT
const CLIENT_APP_NAME 	= 'mySpreadsheets';
const CLIENT_ID       	= '110200043135369381803';
const CLIENT_EMAIL    	= 'newphp-11@appspot.gserviceaccount.com';
const CLIENT_KEY_PATH 	= 'newphp-9c49ef7b78fe.p12'; // path to key-file
const CLIENT_KEY_PW   	= 'notasecret';


?>