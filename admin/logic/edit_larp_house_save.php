<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    print_r($_POST);
    if (isset($_POST['Id']) && !empty($_POST['Id'])) {
        $larp_house = Larp_House::loadById($_POST['Id']);
        
        $larp_house->OrganizerNotes = $_POST['OrganizerNotes'];
        $larp_house->PublicNotes = $_POST['PublicNotes'];
        $larp_house->update();
    } elseif (isset($_POST['HouseId']) && isset($_POST['LARPId'])) {
        $larp_house = Larp_House::newFromArray($_POST);
        $larp_house->create();
    }
    
       
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../view_house.php?id='.$larp_house->HouseId);