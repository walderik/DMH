<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $person = Person::newFromArray($_POST);
        if (Person::SSNAlreadyExists($person->SocialSecurityNumber)) {
            header('Location: ../index.php?error=SSN_already_in_use');
            exit;
        }
        $person->create();
        $person->saveAllNormalAllergyTypes($_POST);
        if (isset($_POST['HouseId']) && $_POST['HouseId']!="null") {
            $housecaretaker = Housecaretaker::newWithDefault();
            $housecaretaker->HouseId = $_POST['HouseId'];
            $housecaretaker->PersonId = $person->Id;
            $housecaretaker->create();
        }
        
    } elseif ($operation == 'update') {
        $person=Person::loadById($_POST['Id']);

        if ($person->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din person
            exit;
        }
        
        $person->setValuesByArray($_POST);
        
        $person->update();
        $person->deleteAllNormalAllergyTypes();
        $person->saveAllNormalAllergyTypes($_POST);
        
        if (isset($_POST['HouseId']) && $_POST['HouseId']!="null") {
            $housecaretaker = Housecaretaker::loadByIds($_POST['HouseId'], $person->Id);
            if (empty($housecaretaker)) {
                $houses=$person->housesOf();
                foreach ($houses as $house) {
                    $housecaretaker = Housecaretaker::loadByIds($house->Id, $person->Id);
                    $housecaretaker->destroy();
                }
                $housecaretaker = Housecaretaker::newWithDefault();
                $housecaretaker->HouseId = $_POST['HouseId'];
                $housecaretaker->PersonId = $person->Id;
                $housecaretaker->create();
            }
        } else {
            $houses = $person->housesOf();
            foreach($houses as $house) {
                $housecaretaker = Housecaretaker::loadByIds($house->Id, $person->Id);
                $housecaretaker->destroy();
                
            }
        }
    } 
    header('Location: ../index.php');
}
