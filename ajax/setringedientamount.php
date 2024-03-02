<?php
include_once 'header_participant.php';

// get the parameters from URL

$supplierIngredientId = $_REQUEST["supplierIngredientId"];
$value = $_REQUEST["value"];



if (empty($supplierIngredientId)|| !isset($value)) {
    return;
}

$supplierIngredient = Alchemy_Supplier_Ingredient::loadById($supplierIngredientId);
$supplierIngredient->Amount = $value;
$supplierIngredient->update();
echo $supplierIngredient->Amount;
