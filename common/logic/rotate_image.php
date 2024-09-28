<?php
include_once '../../participant/header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $image = Image::loadById($id);
        
        $source = imagecreatefromstring($image->file_data);
        $rotate = imagerotate($source,-90,0);
        // New code starts here
        ob_start();
        imagejpeg($rotate);
        $imageData = ob_get_clean();
        
        $image->file_data = $imageData;
        $image->update();
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}



if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

header('Location: ../index.php?message=image_deleted');
exit;
