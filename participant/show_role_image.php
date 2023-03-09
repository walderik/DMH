<?php 


require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $ImageId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}
$ih = Image::newWithDefault();
$image = $ih->load($ImageId);




echo '<img src="data:image/jpeg;base64,'.base64_encode($image).'"/>';
?>
