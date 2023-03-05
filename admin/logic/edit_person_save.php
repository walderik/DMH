
<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $personId = $_POST['PersonId'];

    $personArr = $_POST;
    $personArr += ["Id" => $personId];
    
    $person = Person::loadById($personId);
    $person->setValuesByArray($personArr);
    $person->update();
        
    $person->deleteAllNormalAllergyTypes();
    $person->saveAllNormalAllergyTypes($_POST);
    
    
    $registrationId = $_POST['RegistrationId'];
    $registrationArr = $_POST;
    $registrationArr += ["Id" => $registrationId];
    
    $registration = Registration::loadById($registrationId);

    $registration->setValuesByArray($registrationArr);
    $registration->update();
    
    $registration->deleteAllOfficialTypes();
    $registration->saveAllOfficialTypes($_POST);
    
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');