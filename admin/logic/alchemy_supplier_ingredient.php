<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = $_GET['operation'];
    if ($operation == "delete") {
        Alchemy_Supplier_Ingredient::delete($_GET['id']);
        header('Location: ../view_alchemy_supplier.php?id='.$_GET['supplierId']);
        exit;
    }
}
header('Location: ../index.php?');
exit;


