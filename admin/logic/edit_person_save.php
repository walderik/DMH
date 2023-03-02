<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}



echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $personId = $_POST['PersonId'];
    $personArr = $_POST;
    $personArr += ["Id" => $personId];
    
    $person = Person::newFromArray($personArr);
    $person->update();
    
    
    $registrationId = $_POST['RegistrationId'];
    $registrationArr = $_POST;
    $registrationArr += ["Id" => $registrationId];
    
    $registration = Registration::newFromArray($registrationArr);
    $registration->update();
    
    echo "<br><br>";
    /*
    echo "Person: ";
    print_r($person);
    echo "<br><br>";
    print_r($personArr);
    echo "<br><br>";
    */
    echo "<br><br>";
    echo "Reg: ";
    print_r($registration);
    echo "<br><br>";
    print_r($registrationArr);
    //header('Location: ../edit_person.php?id='.$personId);
    exit;
           
}
//header('Location: ../index.php');
