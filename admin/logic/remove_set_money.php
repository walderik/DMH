<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['roleId']) && isset($_GET['larpId'])) {
        $larp_role = LARP_Role::loadByIds($_GET['roleId'], $_GET['larpId']);
        $larp_role->StartingMoney = null;
        $larp_role->update();
        header('Location: ../roles_money.php');
        exit;

    } elseif (isset($_GET['groupId']) && isset($_GET['larpId'])) {
        $larp_group = LARP_Group::loadByIds($_GET['groupId'], $_GET['larpId']);
        $larp_group->StartingMoney = null;
        $larp_group->update();
        header('Location: ../groups_money.php');
        exit;
        
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}



header('Location: ../housing.php');
exit;
