<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if (!$current_larp->mayRegister()) {
    header('Location: ../index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $larp_group = LARP_Group::newFromArray($_POST);
        $larp_group->create();
        BerghemMailer::send_groupregistration_mail($larp_group, $current_person);
    } 
    header('Location: ../index.php');
}
