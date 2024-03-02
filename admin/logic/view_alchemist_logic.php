<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'update') {
        $alchemist=Alchemy_Alchemist::loadById($_POST['Id']);
        $alchemist->setValuesByArray($_POST);
        $alchemist->update();
    } elseif ($operation == "add_alchemist_recipe") {
        $alchemist=Alchemy_Alchemist::loadById($_POST['Id']);
        if (isset($_POST['RecipeId'])) $alchemist->addRecipes($_POST['RecipeId'], $current_larp, true);
    } elseif ($operation == "add_alchemist") {
        if (isset($_POST['RoleId'])) Alchemy_Alchemist::createAlchemists($_POST['RoleId'], $current_larp);
    } elseif ($operation == "add_recipe_alchemist") {
        $recipe = Alchemy_Recipe::loadById($_POST['Id']);
        if (isset($_POST['AlchemistId'])) $recipe->addToAlchemists($_POST['AlchemistId'], $current_larp);
    }
}



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) $alchemist=Alchemy_Alchemist::loadById($_GET['id']);
    $operation = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == "remove_recipe") {
        $alchemist->removeRecipe($_GET['RecipeId']);
    } elseif ($operation == 'delete') {
        Alchemy_Alchemist::delete($_GET['id']);
    }
}



$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : $_SERVER['HTTP_REFERER'];
if (empty($referer)) $referer = "../magic_magician_admin.php";
header('Location: ' . $referer);

