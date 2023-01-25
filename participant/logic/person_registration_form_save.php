<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $registration = Registration::newFromArray($_POST);
        $registration->create();
        $registration->saveAllIntrigueTypes($_POST);
        //TODO spara alla valdra roller i LARP_Role
    } elseif ($operation == 'delete') {
        $larp_group->deleteAllIntrigueTypes();
        Registration::delete($_POST['Id']);
        
        //Ta bort valda roller
    } elseif ($operation == 'update') {
        
        $registration = Registration::newFromArray($_POST);
        $registration->update();
        $registration->deleteAllIntrigueTypes();
        $registration->saveAllIntrigueTypes($_POST);
        
        //Uppdatera valda roller
        
    } else {
        echo $operation;
    }
    header('Location: ../index.php');
}
