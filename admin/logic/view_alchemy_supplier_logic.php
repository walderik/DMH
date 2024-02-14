<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'update') {
        $supplier=Alchemy_Supplier::loadById($_POST['Id']);
        $supplier->setValuesByArray($_POST);
        $supplier->update();
    } elseif ($operation == "add_alchemy_supplier") {
        if (isset($_POST['RoleId'])) Alchemy_Supplier::createSuppliers($_POST['RoleId'], $current_larp);
    }
}



$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : $_SERVER['HTTP_REFERER'];
if (empty($referer)) $referer = "../alchemy_supplier_admin.php";
header('Location: ' . $referer);

