<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
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
    

    $supplier = Alchemy_Supplier::getForRole($role);
    if (isset($_POST['IngridientId'])) $supplier->addIngredientsForLARP($_POST['IngridientId'], $current_larp);
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../view_alchemy_supplier.php?id='.$role->Id;
header('Location: ' . $referer);
