<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['houseId'])) {
        $larp_house = Larp_House::loadByIds($_POST['houseId'], $current_larp->Id);
        $larp_house->CleaningStatus = Larp_House::READY_FOR_INSPECTION;
        $larp_house->StatusPerson = $current_person->Id;
        $now = new Datetime();
        $larp_house->StatusTime = date_format($now,"Y-m-d H:i:s");
        $larp_house->update();
    }
}

header('Location: ../index.php');

