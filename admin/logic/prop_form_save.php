<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $prop = Prop::newFromArray($_POST);
        $prop->create();
    } elseif ($operation == 'delete') {
        Prop::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $prop=Prop::loadById($_POST['Id']);
        $prop->setValuesByArray($_POST);
        $prop->update();
    }
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../prop_admin.php';
header('Location: ' . $referer);
