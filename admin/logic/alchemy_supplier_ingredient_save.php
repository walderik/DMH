<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $supplier = Alchemy_Supplier::loadById($_POST['Id']);
    if (isset($_POST['IngridientId'])) $supplier->addIngredientsForLARP($_POST['IngridientId'], $current_larp);
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../view_alchemy_supplier.php?id='.$role->Id;
header('Location: ' . $referer);
