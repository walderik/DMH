<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['unapprove'])) {
        if (isset($_GET['id'])) {
            $supplier_ingredient = Alchemy_Supplier_Ingredient::loadById($_GET['id']);
            $supplier_ingredient->IsApproved = 0;
            $supplier_ingredient->update();
        } elseif (isset($_GET['all']) && isset($_GET['supplierId'])) {
            $supplier = Alchemy_Supplier::loadById($_GET['supplierId']);
            $amounts = $supplier->getIngredientAmounts($current_larp);
            foreach($amounts as $amount) {
                if ($amount->isApproved()) {
                    $amount->IsApproved = 0;
                    $amount->update();
                }
            }
        }
    }
    else {
        if (isset($_GET['id'])) {
            $supplier_ingredient = Alchemy_Supplier_Ingredient::loadById($_GET['id']);
            $supplier_ingredient->IsApproved = 1;
            $supplier_ingredient->update();
        } elseif (isset($_GET['all']) && isset($_GET['supplierId'])) {
            $supplier = Alchemy_Supplier::loadById($_GET['supplierId']);
            $amounts = $supplier->getIngredientAmounts($current_larp);
            foreach($amounts as $amount) {
                if (!$amount->isApproved()) {
                    $amount->IsApproved = 1;
                    $amount->update();
                }
             }
        }
    }
    header('Location: ../view_alchemy_supplier.php?id='.$_GET['supplierId']);
    exit;
    
}
header('Location: ../index.php?');
exit;


