<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $essence = Alchemy_Essence::newFromArray($_POST);
        $essence->create();
    } elseif ($operation == 'delete') {
        Alchemy_Essence::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $essence=Alchemy_Essence::loadById($_POST['Id']);
        $essence->setValuesByArray($_POST);
        $essence->update();
    }
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../prop_admin.php';
header('Location: ' . $referer);
