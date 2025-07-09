<?php

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $role = Role::loadById($_POST['Id']);
    
    if (!$role->userMayEdit()) {
        //Inte tillåtelse att redigera
        header('Location: ../index.php?error=');
        exit;
    }
    if ($role->isPC() && $role->PersonId != $current_person->Id) {
        header('Location: ../index.php'); //Inte din karaktär
        exit;
    }
    $group = $role->getGroup();
    if ($role->isNPC() && !empty($group) && !$current_person->isMemberGroup($group)) {
        header('Location: ../index.php'); //NPC som inte är med i din grupp
        exit;
    }
    
    
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    if ($role->isNPC() || !empty($larp_role)) {
     $role->UserMayEdit = 0;
     $role->update();
    }
         
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
 
    header('Location: ../view_role.php?id='.$role->Id);
}
