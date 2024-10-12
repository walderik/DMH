<?php

// All kod som skall köras först på varje sida gemensamt oavsett om det rör admin-header eller annan header

$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";




if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        require_once $root . '/includes/init.php';
        // If the user is not logged in redirect to the login page...
        if (!isset($_SESSION['is_loggedin'])) {
            header('Location: index.php');
            exit;
        }
        
        
        $imageId = $_GET['id'];
        $image = Image::loadById($imageId);
        
        //header("Content-type: $image->file_mime");
        // give the browser an indication of the size of the image
        header('Content-Length: ' . strlen($image->file_data));
        
        header("Content-type: ".$image->file_mime);
        
        echo $image->file_data;
        
        
    } elseif (isset($_GET['Swish'])) {
        include_once $root . '/includes/all_includes.php';
        $registration = Registration::loadById($_GET['RegistrationId']);
        $campaign = Campaign::loadById($_GET['CampaignId']);
        $response = Swish::QRCode($registration, $campaign);        
 
        header('Content-Length: ' . strlen($response));
        
        header("Content-type: image/png");
        
        echo $response;
        
    }
}




