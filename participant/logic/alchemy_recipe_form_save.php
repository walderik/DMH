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
    if (!Alchemy_Alchemist::isAlchemist($role)) {
        header('Location: ../index.php'); // karaktären är inte alkemist
        exit;
    }
    if ($role->Id != $_POST['AuthorRoleId']) {
        header('Location: index.php'); // inte ditt recept
        exit;
    }
    
    
    if ($operation == 'insert') {
        $recipe = Alchemy_Recipe::newFromArray($_POST);
        $recipe->create();
        $alchemist = Alchemy_Alchemist::getForRole($role);
        $alchemist->addRecipes(array($recipe->Id), $current_larp, false);
    } elseif ($operation == 'update') {
        $recipe=Alchemy_Recipe::loadById($_POST['Id']);
        if ($recipe->isApproved() || $role->Id !=$recipe->AuthorRoleId) {
            header('Location: index.php'); // receptet får inte redigeras eller inte ditt recept
            exit;
        }
        $recipe->setValuesByArray($_POST);
        $recipe->update();
    } else {
        header('Location: ../index.php');
        exit;
    }
    
    if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
        $recipe->deleteAllIngredients();
        $recipe->saveAllIngredients($_POST['IngredientId']);
    } elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
        $recipe->deleteAllEssences();
        $recipe->saveAllEssences($_POST['Essences']);}
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../alchemy_all_recipes.php';
header('Location: ' . $referer);
