<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $resource = Resource::newFromArray($_POST);
        $resource->create();
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $resource=Resource::loadById($_POST['Id']);
        $resource->setValuesByArray($_POST);
        $resource->update();
    }
}

$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../resource_admin.php';
header('Location: ' . $referer);

