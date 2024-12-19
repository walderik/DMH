<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    $RoleId = $_POST['RoleId'];
    
    $role = Role::loadById($RoleId);
    
    if ($role->PersonId != $current_person->Id) {
        header('Location: ../index.php'); //Inte din karaktär
        exit;
    }
    
    if (!$role->isRegistered($current_larp)) {
        header('Location: ../index.php'); // karaktären är inte anmäld
        exit;
    }
    if (!Alchemy_Supplier::isSupplier($role)) {
        header('Location: ../index.php'); // karaktären är inte lövjerist
        exit;
    }
    
    
    
    if ($operation == 'insert') {
        $ingredient = Alchemy_Ingredient::newFromArray($_POST);
        $ingredient->create();
        
        $essenceIds = array();

        if (isset($_POST['essence1']) && $_POST['essence1']!="null") $essenceIds[] = $_POST['essence1'];
        if (isset($_POST['essence2']) && $_POST['essence2']!='null') $essenceIds[] = $_POST['essence2'];
        if (isset($_POST['essence3']) && $_POST['essence3']!='null') $essenceIds[] = $_POST['essence3'];
        $ingredient->setEssences($essenceIds);
        
    } elseif ($operation == 'delete') {
        Alchemy_Ingredient::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $ingredient=Alchemy_Ingredient::loadById($_POST['Id']);
        $ingredient->setValuesByArray($_POST);
        $ingredient->update();

        $essenceIds = array();
        if (isset($_POST['essence1']) && $_POST['essence1']!="null") $essenceIds[] = $_POST['essence1'];
        if (isset($_POST['essence2']) && $_POST['essence2']!='null') $essenceIds[] = $_POST['essence2'];
        if (isset($_POST['essence3']) && $_POST['essence3']!='null') $essenceIds[] = $_POST['essence3'];
        $ingredient->deleteEssences();
        $ingredient->setEssences($essenceIds);
        
    }
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../alchemy_ingredient_admin.php';
header('Location: ' . $referer);
