<?php 

date_default_timezone_set('Europe/Helsinki');

const CLIENT_ID 			= '958391346195-2io8faetghst4eumgdik32q3rv5u9jh4.apps.googleusercontent.com';
const CLIENT_SECRET 		= 'BodELQQQQ_nmY53e4aEa3s9q';
//const REDIRECT_URI 			= 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];// fits both fetchandshow.herokuapp.com and localhost

/**
 ****************** SPREADSHEET *****************************
 */

// ACTIVE SPREADSHEET AND WORKSHEET (stay unchanged)
const SPREADSHEET_TITLE 	= "report";
const WORKSHEET_TITLE 		= "Data";
const SPREADSHEET_URL 		= "https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/";

// SERVICE ACCOUNT
const CLIENT_APP_NAME_W 	= 'mySpreadsheets';
const CLIENT_ID_W       	= '110200043135369381803';
const CLIENT_EMAIL_W    	= 'newphp-11@appspot.gserviceaccount.com';
const CLIENT_KEY_PATH_W 	= 'newphp-9c49ef7b78fe.p12'; // path to key-file
const CLIENT_KEY_PW_W   	= 'notasecret';


?>