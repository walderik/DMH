<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $supplier_ingredient = Alchemy_Supplier_Ingredient::loadById($_GET['id']);
    if (isset($supplier_ingredient)) {
        
        $supplier_ingredient->IsApproved = 1;
        $supplier_ingredient->update();
        header('Location: ../view_alchemy_supplier.php?id='.$_GET['supplierId']);
        exit;
    }
    
}
header('Location: ../index.php?');
exit;


