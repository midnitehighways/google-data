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
    $_SESSION['access_token'] = $client->getAccessToken();

    $dr_results = $dr_service->files->listFiles(array());

    $yt_channels = $yt_service->channels->listChannels('contentDetails', array("mine" => true));

    $likePlaylist = $yt_channels[0]->contentDetails->relatedPlaylists->likes;
    $yt_results = $yt_service->playlistItems->listPlaylistItems(
          "snippet",
          array("playlistId" => $likePlaylist, 'maxResults' => 50)
    );
  //$yt_videos = $yt_service->videos->listVideos('contentDetails', array("playlistId" => $likePlaylist));
}

echo pageHeader("Fetch Google Data and Show Stats");
?>



<?php 
echo '<div class="left">';
if (isset($authUrl)) {
    echo '<br><br><br><br><p>Welcome! This app needs your permission to get access to your Google Drive and Youtube accounts</p>
    <p>Please click "Start" to continue</p><br><br>
    <a class="my-button" href="' . $authUrl . '">Start</a> <br />';
} else {
    
    echo '<a class="my-button" href="/?logout">Log out</a><br /><br />';
    
    echo '<p>Display and systematize data from Google Drive</p>
        <form method="POST">    
<div class="drive">
            <span class=""> Consider trashed files <input type="checkbox" name="trashed" checked /></span><br/><br/>
            <input type="submit" class="my-button" value="File types" name="submit"><br/><br/>
            <input type="submit" class="my-button" value="Time created" name="year_created"><br/><br/>
            <input type="submit" class="my-button" value="List Drive files" name="list_files">
</div>
            <br/><p>Retrive some data from YouTube</p>
<div class="youtube">
            <input type="submit" class="my-button" value="Time liked" name="year_liked"><br/><br/>
            <input type="submit" class="my-button" value="List liked videos" name="list_videos"><br/>
</div>
        </form>';

    $drive_filetypes = array();
    $drive_created_dates = array();
    $youtube_like_dates = array();
    //var_dump($_POST);

    foreach ($dr_results as $item) {
        if((isset($_POST['trashed'])) || (!$item->labels->trashed)) {       // either 'show trashed' checked or file isn't trashed
//            echo $item->title, "<br /> \n";
  
            $datetime = new DateTime($item->createdDate);
            array_push($drive_created_dates, $datetime->format('Y'));

            $info = new SplFileInfo($item->title);           // using this class to get file extension
            if($info->getExtension()) {
              array_push($drive_filetypes, strtolower($info->getExtension()));
            }
            else {                                           // no extension
              array_push($drive_filetypes, "none");
            }
        }
    }
  
  //print_r(array_count_values($drive_created_dates));

  foreach ($yt_results as $item) {
    $datetime = new DateTime($item['snippet']['publishedAt']);
    array_push($youtube_like_dates, $datetime->format('Y'));
  }
}




if(isset($_POST["submit"])) {
    display_drive_data(array_count_values($drive_filetypes), "type", "number", 2);
}

if(isset($_POST["year_created"])) {
    display_drive_data(array_count_values($drive_created_dates), "year", "filenumber", 2);
}

if(isset($_POST["year_liked"])) {
    display_drive_data(array_count_values($youtube_like_dates), "yearliked", "likenumber", 2);
}

if(isset($_POST["list_videos"])) {
    echo "<h4>Your YouTube likes</h4><ul>";
    foreach ($yt_results as $item)
        echo '<li>' . $item["snippet"]["title"] . '</li>';
    echo "</ul>";
}

if(isset($_POST["list_files"])) {
    echo "<h4>Files on your Google Drive</h4><ul>";
    foreach ($dr_results as $item) {
        if((isset($_POST['trashed'])) || (!$item->labels->trashed)) {       // either 'show trashed' checked or file isn't trashed
            echo '<li>' . $item->title . '</li>';
        }
    }
    echo "</ul>";
}
echo '</div>';
echo '<iframe class="spreadsheet" src="https://docs.google.com/spreadsheets/d/11kI3ihoDbGsrVSOVfr1UMCQr7k3TE0a-oOlaCrtFYlE/edit#gid='.get_data_worksheet_id().'"></iframe>';

?>