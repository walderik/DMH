<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['SupplierId'])) {
        $supplier = Alchemy_Supplier::loadById($_POST['SupplierId']);
        $person = $supplier->getRole()->getPerson();

    } else {
        
        header('Location: ../alchemy_supplier_admin.php');
        exit;
    }
}


if (!isset($supplier) && !isset($person)) {
    header('Location: ../alchemy_supplier_admin.php');
    exit;
}

if (!is_null($person) && $person->isNotComing($current_larp)) {
    Alchemy_Supplier_Ingredient::deleteAllForSupplier($supplier->Id);
}


header('Location: ../alchemy_supplier_admin.php');

