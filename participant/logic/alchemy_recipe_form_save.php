<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    $RoleId = $_POST['RoleId'];
    
    $role = Role::loadById($RoleId);
    $person = $role->getPerson();
    
    if ($person->UserId != $current_user->Id) {
        header('Location: ../index.php'); //Inte din karaktär
        exit;
    }
    
    if (!$role->isRegistered($current_larp)) {
        header('Location: ../index.php'); // karaktären är inte anmäld
        exit;
    }
    if (!Alchemy_Alchemist::isAlchemist($role)) {
        header('Location: ../index.php'); // karaktären är inte alkemist
        exit;
    }
    
    
    if ($operation == 'insert') {
        $recipe = Alchemy_Recipe::newFromArray($_POST);
        $recipe->create();
    } else {
        header('Location: ../index.php');
        exit;
    }
    
    if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
        $recipe->saveAllIngredients($_POST['IngredientId']);
    } elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
        $recipe->saveAllEssences($_POST['Essences']);}
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../alchemy_all_recipes.php';
header('Location: ' . $referer);
