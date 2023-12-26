<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

//Ifthe user isnt admin it may not see these pages
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}



copy_from_person();



function copy_from_person() {
    $persons = Person::all();
    foreach ($persons as $person) {
        if (isset($person->HouseId)) {
            $housecaretaker = Housecaretaker::loadByIds($person->HouseId, $person->Id);
            if (empty($housecaretaker)) {
                $housecaretaker = Housecaretaker::newWithDefault();
                $housecaretaker->IsApproved = 0;
                $housecaretaker->PersonId = $person->Id;
                $housecaretaker->HouseId = $person->HouseId;
                $housecaretaker->create();
            }
        }
    }
}

