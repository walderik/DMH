<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['RoleId'])) {
        $larp_role = LARP_Role::loadByIds($_POST['RoleId'], $current_larp->Id);
        if (!isset($larp_role)) {
            header('Location: ../index.php');
            exit;
        }
        $role = Role::loadById($larp_role->RoleId);
    
        if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din karaktär
            exit;
        }
        $larp_role->WhatHappened = $_POST['WhatHappened'];
        $larp_role->WhatHappendToOthers = $_POST['WhatHappendToOthers'];
        $larp_role->update();
    
        if (isset($_POST['IngtrigueActorId'])) {
            $intrigueActorIddArr = $_POST['IngtrigueActorId'];
            foreach ($intrigueActorIddArr as $intrigueActorId) {
                $intrigueActor = IntrigueActor::loadById($intrigueActorId);
                if ($intrigueActor->RoleId != $role->Id) continue; //Inte vår aktör
                $intrigueActor->WhatHappened = $_POST["IngtrigueActor_".$intrigueActor->Id];
                $intrigueActor->update();
            }
        }
    } elseif (isset($_POST['GroupId'])) {
        $larp_group = LARP_Group::loadByIds($_POST['GroupId'], $current_larp->Id);
        if (!isset($larp_group)) {
            header('Location: ../index.php');
            exit;
        }
        $group = Group::loadById($larp_group->GroupId);
        
        if (!$current_user->isGroupLeader($group)) {
            header('Location: index.php'); //Inte din grupp
            exit;
        }
        
        $larp_group->WhatHappened = $_POST['WhatHappened'];
        $larp_group->WhatHappendToOthers = $_POST['WhatHappendToOthers'];
        $larp_group->update();
        
        if (isset($_POST['IngtrigueActorId'])) {
            $intrigueActorIddArr = $_POST['IngtrigueActorId'];
            foreach ($intrigueActorIddArr as $intrigueActorId) {
                $intrigueActor = IntrigueActor::loadById($intrigueActorId);
                if ($intrigueActor->GroupId != $group->Id) continue; //Inte vår aktör
                $intrigueActor->WhatHappened = $_POST["IngtrigueActor_".$intrigueActor->Id];
                $intrigueActor->update();
            }
        }
    }
        
    
}

header('Location: ../index.php');