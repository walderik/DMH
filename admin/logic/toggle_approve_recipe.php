<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset( $_POST['recipeId'])) {
        header('Location: ../index.php?post');
        exit;
    }
    $recipeId = $_POST['recipeId'];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset( $_GET['recipeId'])) {
        header('Location: ../index.php?get');
        exit;
    }
    $recipeId = $_GET['recipeId'];
    
}

if (!isset($recipeId)) {
    header('Location: ../index.php?id');
    exit;
}


$recipe = Alchemy_Recipe::loadById($recipeId);


if ($recipe->isApproved()) $recipe->IsApproved = 0;
else $recipe->IsApproved = 1;

$recipe->update();

if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../alchemy_recipe_admin.php');
