<?php
include_once '../header.php';

//print_r($_POST);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $intrigue = Intrigue::newFromArray($_POST);
        $intrigue->create();
        if (isset($_POST['IntrigueTypeId'])) $intrigue->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
    } elseif ($operation == 'update') {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->setValuesByArray($_POST);
        $intrigue->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) $intrigue->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
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
    } elseif ($operation == "add_intrigue_npcgroup") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['NPCGroupId'])) $intrigue->addNPCGroups($_POST['NPCGroupId']);
    } elseif ($operation == "add_intrigue_message") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['TelegramId'])) $intrigue->addTelegrams($_POST['TelegramId']);
        if (isset($_POST['LetterId'])) $intrigue->addLetters($_POST['LetterId']);
    } elseif ($operation == "add_intrigue_pdf") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_FILES['bilaga'])) $intrigue->addPdf();
    } elseif ($operation == "add_intrigue_relation") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['IntrigueId'])) $intrigue->addIntrigueRelations($_POST['IntrigueId']);
    } elseif ($operation == "update_intrigue_actor") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->IntrigueText = $_POST['IntrigueText'];
        $intrigueActor->OffInfo = $_POST['OffInfo'];
        $intrigueActor->update();
    } elseif ($operation == "choose_intrigue_checkin") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        if (isset($_POST['Intrigue_PropId'])) $intrigueActor->addCheckinProps($_POST['Intrigue_PropId']);
        if (isset($_POST['Intrigue_LetterId'])) $intrigueActor->addCheckinLetters($_POST['Intrigue_LetterId']);
        if (isset($_POST['Intrigue_TelegramId'])) $intrigueActor->addCheckinTelegrams($_POST['Intrigue_TelegramId']);
    } elseif ($operation == "choose_intrigue_knownprops") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        if (isset($_POST['Intrigue_PropId'])) $intrigueActor->addKnownProps($_POST['Intrigue_PropId']);
        if (isset($_POST['Intrigue_PdfId'])) $intrigueActor->addKnownPdf($_POST['Intrigue_PdfId']);
    } elseif ($operation == "choose_intrigue_knownactors") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        if (isset($_POST['KnownIntrigueActorId'])) $intrigueActor->addKnownActors($_POST['KnownIntrigueActorId']);
        if (isset($_POST['Intrigue_NPCGroupId'])) $intrigueActor->addKnownNPCGroups($_POST['Intrigue_NPCGroupId']);
        if (isset($_POST['Intrigue_NPCId'])) $intrigueActor->addKnownNPCs($_POST['Intrigue_NPCId']);
    } elseif ($operation == "continue_intrigue") {
        if (isset($_POST['IntrigueId'])) Intrigue::continueIntrigues($_POST['IntrigueId'], $current_larp, $current_user);
        header('Location: ../intrigue_admin.php');
        exit;
     } else {
        $intrigue=Intrigue::loadById($_POST['Id']);
    }
    
    
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $intrigue=Intrigue::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
        
    if ($operation == "remove_intrigueactor") {
        IntrigueActor::delete($_GET['IntrigueActorId']);
    } elseif ($operation == "remove_prop") {
        Intrigue_Prop::delete($_GET['IntriguePropId']);
    } elseif ($operation == "remove_npc") {
        Intrigue_NPC::delete($_GET['IntrigueNPCId']);
    } elseif ($operation == "remove_npcgroup") {
        Intrigue_NPCGroup::delete($_GET['IntrigueNPCGroupId']);
    } elseif ($operation == "remove_letter") {
        Intrigue_Letter::delete($_GET['IntrigueLetterId']);
    } elseif ($operation == "remove_telegram") {
        Intrigue_Telegram::delete($_GET['IntrigueTelegramId']);        
    } elseif ($operation == "remove_intrigue_relation") {
        Intrigue::removeRelation($_GET['IntrigueRelationId'], $_GET['Id']);
    } elseif ($operation == "remove_letter_checkin") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeLetter($_GET['LetterId']);
    } elseif ($operation == "remove_telegram_checkin") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeTelegram($_GET['TelegramId']);
    } elseif ($operation == "remove_prop_checkin") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removePropCheckin($_GET['PropId']);
    } elseif ($operation == "remove_prop_known") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownProp($_GET['PropId']);
    } elseif ($operation == "remove_pdf_known") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownPdf($_GET['PdfId']);
    } elseif ($operation == "remove_intrigueactor_knownRole") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownRole($_GET['RoleId']);        
    } elseif ($operation == "remove_intrigueactor_knownGroup") {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownGroup($_GET['GroupId']);
    } elseif (($operation == "remove_npcgroup_intrigueactor")) {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownNPCGroup($_GET['NPCGroupId']);
    } elseif (($operation == "remove_npc_intrigueactor")) {
        $intrigueActor=IntrigueActor::loadById($_GET['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->removeKnownNPC($_GET['NPCId']);
    } elseif (($operation == "delete_pdf")) {
        $intriguePdf=Intrigue_Pdf::loadById($_GET['pdfId']);
        $intrigue=$intriguePdf->getIntrigue();
        Intrigue_Pdf::delete($intriguePdf->Id);
    }
}


if (isset($_POST['Section'])) $section = "#".$_POST['Section'];
elseif (isset($_GET['Section'])) $section = "#".$_GET['Section'];
else $section = "";

if (isset($_POST['Referer']) && $_POST['Referer']!="") {
    header('Location: ' . $_POST['Referer'].$section);
    exit;
}

header("location: ../view_intrigue.php?Id=$intrigue->Id$section");
