<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $titledeed = Titledeed::newFromArray($_POST);
        $titledeed->create();
        if (isset($_POST['ProducesId'])) {
            $titledeed->setProduces($_POST['ProducesId']);
        }
        if (isset($_POST['RequiresId'])) {
            $titledeed->setRequires($_POST['RequiresId']);
        }
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $titledeed = Titledeed::loadById($_POST['Id']);
        $titledeed->setValuesByArray($_POST);
        $titledeed->update();
        $titledeed->deleteAllProduces();
        $titledeed->deleteAllRequires();
        if (isset($_POST['ProducesId'])) {
            $titledeed->setProduces($_POST['ProducesId']);
        }
        if (isset($_POST['RequiresId'])) {
            $titledeed->setRequires($_POST['RequiresId']);
        }
    } elseif ($operation == 'add_titledeed_owner_role') {
        $titledeed = Titledeed::loadById($_POST['Id']);
        if (isset($_POST['RoleId'])) $titledeed->addRoleOwners($_POST['RoleId']);
        
    } elseif ($operation == 'add_titledeed_owner_group') {
        $titledeed = Titledeed::loadById($_POST['Id']);
        if (isset($_POST['GroupId'])) $titledeed->addGroupOwners($_POST['GroupId']);
        
    }
}

$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../resource_admin.php';
header('Location: ' . $referer);

