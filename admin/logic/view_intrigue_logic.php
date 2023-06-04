<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $intrigue = Intrigue::newFromArray($_POST);
        $intrigue->create();
    } elseif ($operation == 'update') {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->setValuesByArray($_POST);
        $intrigue->deleteAllIntrigueTypes();
        $intrigue->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        $intrigue->update();
    } elseif ($operation == "add_intrigue_actor_role") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['RoleId'])) $intrigue->addRoleActors($_POST['RoleId']);
    } elseif ($operation == "exhange_intrigue_actor_role") {
        $intrigueActor=IntrigueActor::loadById($_POST['Id']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->RoleId = $_POST['RoleId'];
        $intrigueActor->GroupId=null;
        $intrigueActor->update();
    } elseif ($operation == "exhange_intrigue_actor_group") {
        $intrigueActor=IntrigueActor::loadById($_POST['Id']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->GroupId = $_POST['GroupId'];
        $intrigueActor->RoleId=null;
        $intrigueActor->update();      
    } elseif ($operation == "add_intrigue_actor_group") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['GroupId'])) $intrigue->addGroupActors($_POST['GroupId']);
    } elseif ($operation == "add_intrigue_prop") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['PropId'])) $intrigue->addProps($_POST['PropId']);
    } elseif ($operation == "add_intrigue_npc") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['NPCId'])) $intrigue->addNPCs($_POST['NPCId']);
    } elseif ($operation == "add_intrigue_message") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['TelegramId'])) $intrigue->addTelegrams($_POST['TelegramId']);
        if (isset($_POST['LetterId'])) $intrigue->addLetters($_POST['LetterId']);
    } elseif ($operation == "update_intrigue_actor") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->IntrigueText = $_POST['IntrigueText'];
        $intrigueActor->Offinfo = $_POST['Offinfo'];
        $intrigueActor->update();
    } else {
        $intrigue=Intrigue::loadById($_POST['Id']);
    }
    
    
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $intrigue=Intrigue::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation']))
        $operation = $_GET['operation'];
        
        if ($operation == "remove_intrigueactor") {
            IntrigueActor::delete($_GET['IntrigueActorId']);
        } elseif ($operation == "remove_prop") {
            Intrigue_Prop::delete($_GET['IntriguePropId']);
        } elseif ($operation == "remove_npc") {
            Intrigue_NPC::delete($_GET['IntrigueNPCId']);
        } elseif ($operation == "remove_letter") {
            Intrigue_Letter::delete($_GET['IntrigueLetterId']);
        } elseif ($operation == "remove_telegram") {
            Intrigue_Telegram::delete($_GET['IntrigueTelegramId']);
        }
}

header("location: ../view_intrigue.php?Id=$intrigue->Id");
