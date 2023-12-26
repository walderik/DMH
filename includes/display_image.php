<?php

// All kod som skall köras först på varje sida gemensamt oavsett om det rör admin-header eller annan header

$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/init.php';

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['is_loggedin'])) {
    header('Location: index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $imageId = $_GET['id'];
    }
    else {
        exit;
    }
}

$image = Image::loadById($imageId);

//header("Content-type: $image->file_mime");
// give the browser an indication of the size of the image
header('Content-Length: ' . strlen($image->file_data));

header("Content-type: ".$image->file_mime);

echo $image->file_data;



