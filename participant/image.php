<?php

require 'header.php';


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



