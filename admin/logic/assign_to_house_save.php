<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id']) && isset($_POST['type']) && isset($_POST['HouseId'])) {
        if ($_POST['type']=="group") {
            $group = Group::loadById($_POST['id']);
            if (!empty($group)) {
                $group_members = Person::getPersonsInGroup($group, $current_larp);
                foreach ($group_members as $person) {
                    assign($person, $_POST['HouseId'], $current_larp);
                }
                //Hämta alla i gruppen utan hus och tilldela dem till huset
            }
        }
        else if ($_POST['type']=="person") {
            $person = Person::loadById($_POST['id']);
            assign($person, $_POST['HouseId'], $current_larp);
            

        }
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}



header('Location: ../housing.php');
exit;


function assign(Person $person, $houseId, LARP $larp) {
    //Om man inte har boende ska man tilldelas till huset
    if (!$person->hasHousing($larp)) {
        $housing = Housing::newWithDefault();
        $housing->HouseId = $houseId;
        $housing->PersonId = $person->Id;
        $housing->LARPId = $larp->Id;
        $housing->create();
    }
    
}
