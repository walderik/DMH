<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
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

    
    $alchemist = Alchemy_Alchemist::getForRole($role);

    if (isset($_POST['RecipeId'])) $alchemist->addRecipes($_POST['RecipeId'], $current_larp, false);
      
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../view_alchemist.php?id='.$role->Id;
header('Location: ' . $referer);
