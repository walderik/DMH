<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['RoleId'])) {
        $role=Role::loadById($_POST['RoleId']);
        if ($role->isPC()) {
            if ($role->PersonId != $current_person->Id) {
                header('Location: index.php'); //Inte din karaktär
                exit;
            }
            $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
            if (!isset($larp_role)) {
                header('Location: ../index.php');
                exit;
            }
            $larp_role->WhatHappened = $_POST['WhatHappened'];
            $larp_role->WhatHappendToOthers = $_POST['WhatHappendToOthers'];
            $larp_role->WhatHappensAfterLarp = $_POST['WhatHappensAfterLarp'];
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
        } else {
            $assignment = NPC_assignment::getAssignment($role, $current_larp);
            if (!isset($assignment)) {
                header('Location: ../index.php');
                exit;
            }
            if ($assignment->PersonId != $current_person->Id) {
                header('Location: index.php'); //Inte din karaktär
                exit;
            }
            
            $assignment->WhatHappened = $_POST['WhatHappened'];
            $assignment->WhatHappendToOthers = $_POST['WhatHappendToOthers'];
            $assignment->update();
            
            if (isset($_POST['IngtrigueActorId'])) {
                $intrigueActorIddArr = $_POST['IngtrigueActorId'];
                foreach ($intrigueActorIddArr as $intrigueActorId) {
                    $intrigueActor = IntrigueActor::loadById($intrigueActorId);
                    if ($intrigueActor->RoleId != $role->Id) continue; //Inte vår aktör
                    $intrigueActor->WhatHappened = $_POST["IngtrigueActor_".$intrigueActor->Id];
                    $intrigueActor->update();
                }
            }
            
        }
        
    } elseif (isset($_POST['GroupId'])) {
        $larp_group = LARP_Group::loadByIds($_POST['GroupId'], $current_larp->Id);
        if (!isset($larp_group)) {
            header('Location: ../index.php');
            exit;
        }
        $group = Group::loadById($larp_group->GroupId);
        
        if (!$current_person->isGroupLeader($group)) {
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