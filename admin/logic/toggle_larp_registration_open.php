<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($current_larp->RegistrationOpen == 0) {
    $current_larp->RegistrationOpen = 1;
    $current_larp->update();
}
else {
    $current_larp->RegistrationOpen = 0;
    $current_larp->update();  
}

header('Location: ../index.php');
