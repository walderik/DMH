<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $spell = Magic_Spell::newFromArray($_POST);
        $spell->create();
    } elseif ($operation == 'delete') {
        Magic_Spell::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $school=Magic_Spell::loadById($_POST['Id']);
        $school->setValuesByArray($_POST);
        $school->update();
    }
}

$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../resource_admin.php';
header('Location: ' . $referer);

