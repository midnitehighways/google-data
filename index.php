<?php
session_start();

require 'vendor/autoload.php';
require 'const.php';            // constants, settings, client_secret
require 'client.php';           // set clients, create services for YouTube, Drive, Worksheets
require 'functions.php';        // almost all functions here

/*************************************************************************
  If signed in -> get list of files from Google Drive    -> to $dr_results
                  retrieve 'likes'-playlist from YouTube -> to $yt_results
 *************************************************************************/
if ($client->getAccessToken()) {
    if($client->isAccessTokenExpired()) { // prevent token expiration      ///////////
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
    }                                                                               ///////////////

    $_SESSION['access_token'] = $client->getAccessToken();

    $dr_results = $dr_service->files->listFiles(array());

    $yt_channels = $yt_service->channels->listChannels('contentDetails', array("mine" => true));
    $likePlaylist = $yt_channels[0]->contentDetails->relatedPlaylists->likes;
    $yt_results = $yt_service->playlistItems->listPlaylistItems(
          "snippet",
          array("playlistId" => $likePlaylist, 'maxResults' => 50)
    );
  
    $about = $dr_service->about->get();     // used later to ->getName() of authenticated user
}

echo html_header("Fetch Google Data and Show Stats");

// generate auth URL
if (isset($authUrl)) {                      
    echo html_start($authUrl);

} else {                                    
    echo html_form($about->getName());
    $drive_filetypes = array();
    $drive_created_dates = array();
    $youtube_like_dates = array();
    //var_dump($_POST);


    // HANDLE RESULTS FROM ANALYTICS API
    try {
        $profile = getFirstProfileId($analytics);
        
        // retreive results from Analytics (Reporting) API and print'em
        $results = getResults($analytics, $profile);
        printResults($results);
    }
    
    // catch here the error: when authenticated user got no Analytics account
    catch(Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
        print "<p>No Google Analytics account found</p>";
    }



    // HANDLE DRIVE DATA
    foreach ($dr_results as $item) {
        if((isset($_POST['trashed'])) || (!$item->labels->trashed)) {  // either 'show trashed' option is checked or file isn't trashed

            $datetime = new DateTime($item->createdDate);
            $year = $datetime->format('Y');                            // retrieve year from file's date of creation
            array_push($drive_created_dates, $year);

            $info = new SplFileInfo($item->title);                     // using this class to get file extension
            
            if($info->getExtension()) {
                array_push($drive_filetypes, strtolower($info->getExtension()));
            }
            else {                                                     // if file got no extension -> just put 'none' to resulting array
                array_push($drive_filetypes, "none");
            }
        }
    }
  
    // HANDLE YOUTUBE DATA
    foreach ($yt_results as $item) {
        $datetime = new DateTime($item['snippet']['publishedAt']);
        array_push($youtube_like_dates, $datetime->format('Y'));
    }
}

/**************************
**  Handling user input
***************************/

// Types of files on Drive
if(isset($_POST["type_of_file"])) {
    display_drive_data($drive_filetypes, "type", "number", 1);
}

// Files on Drive by year of creation
if(isset($_POST["year_created"])) {
    display_drive_data($drive_created_dates, "year", "filenumber", 1);
}

// Info about liked YouTube videos by year when it was liked
if(isset($_POST["year_liked"])) {
    display_drive_data($youtube_like_dates, "yearliked", "likenumber", 1);
}

// clear worksheet
if(isset($_GET["clear"])) {
    provide_clear_worksheet();
}

// just display titles of liked videos below the form 
if(isset($_POST["list_videos"])) {
    echo "<h4>Your YouTube likes</h4><ul>";
    foreach ($yt_results as $item)
        echo '<li>' . $item["snippet"]["title"] . '</li>';
    echo "</ul>";
}

// list all files on Google Drive
if(isset($_POST["list_files"])) {
    echo "<h4>Files on your Google Drive</h4><ul>";
    foreach ($dr_results as $item) {
        if((isset($_POST['trashed'])) || (!$item->labels->trashed)) {
            echo '<li>' . $item->title . '</li>';
        }
    }
    echo "</ul>";
}

echo html_closing_part();

?>