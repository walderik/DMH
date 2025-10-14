<?php
include_once 'header.php';
include 'navigation.php';



function whatHappenedGroup(Group $group) {
    global $current_larp;
    $hasWhatHappened = false;
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    if (!empty($larp_group->WhatHappened) || !empty($larp_group->WhatHappenedToOthers)) $hasWhatHappened = true;
    $intrigueActors = array();
    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
    foreach ($intrigues as $intrigue) {
        $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
        if (!empty($intrigueActor->WhatHappened)) {
            $hasWhatHappened = true;
            $intrigueActors[] = $intrigueActor;
        }
    }
    if ($hasWhatHappened) {
        echo "<div class='item'>";
        echo "<h3>";
        echo $group->getViewLink();
        echo "</h3>";

        foreach($intrigueActors as $intrigueActor) {
            $intrigue = $intrigueActor->getIntrigue();
            echo "<h3><a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a></h3>";
            echo nl2br($intrigueActor->WhatHappened);
        }
        if (!empty($larp_group->WhatHappened)) {
            echo "<h3>Vad hände i övrigt med/för $group->Name?</h3>";
            echo nl2br($larp_group->WhatHappened);
        }
        if (!empty($larp_group->WhatHappenedToOthers)) {
            echo "<h3>Vad såg $group->Name hände med andra?</h3>";
            echo nl2br($larp_group->WhatHappenedToOthers);
        }
        if (!empty($larp_group->WhatHappensAfterLarp)) {
            echo "<h3>Vad gör $group->Name fram till nästa lajv?</h3>";
            echo nl2br($larp_group->WhatHappensAfterLarp);
        }
        echo "</div>";
    }
}



function whatHappenedRole(Role $role) {
    global $current_larp;
    $hasWhatHappened = false;
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    $assignment = NPC_assignment::getAssignment($role, $current_larp);
    $person = $role->getPerson();
    if (isset($assignment)) $person = $assignment->getPerson();
        
    if (!empty($larp_role) && (!empty($larp_role->WhatHappened) || !empty($larp_role->WhatHappenedToOthers))) $hasWhatHappened = true;
    elseif (!empty($assignment) && (!empty($assignment->WhatHappened) || !empty($assignment->WhatHappenedToOthers))) $hasWhatHappened = true;
    $intrigueActors = array();
    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
    foreach ($intrigues as $intrigue) {
        $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
        if (!empty($intrigueActor->WhatHappened)) {
            $hasWhatHappened = true;
            $intrigueActors[] = $intrigueActor;
        }
    }
    if ($hasWhatHappened) {
        echo "<div class='item'>";
        echo "<h3>";
        echo $role->getViewLink();
        $group = $role->getGroup();
        if (isset($group)) echo " - " . $group->getViewLink();
        if (!empty($person)) {
            echo ", spelad av ".$person->getViewLink();
        }
        echo "</h3>";
        foreach($intrigueActors as $intrigueActor) {
            $intrigue = $intrigueActor->getIntrigue();
            echo "<h3><a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a></h3>";
            echo nl2br($intrigueActor->WhatHappened);
        }
        
        if (isset($larp_role)) {
            if (!empty($larp_role->WhatHappened)) {
                echo "<h3>Vad hände i övrigt med/för $role->Name?</h3>";
                echo nl2br($larp_role->WhatHappened);
            }
            if (!empty($larp_role->WhatHappenedToOthers)) {
                echo "<h3>Vad såg $role->Name hände med andra?</h3>";
                echo nl2br($larp_role->WhatHappenedToOthers);
            }
            if (!empty($larp_role->WhatHappensAfterLarp)) {
                echo "<h3>Vad gör $role->Name fram till nästa lajv?</h3>";
                echo nl2br($larp_role->WhatHappensAfterLarp);
            }
        } elseif (isset($assignment)) {
            if (!empty($assignment->WhatHappened)) {
                echo "<h3>Vad hände i övrigt med/för $role->Name?</h3>";
                echo nl2br($assignment->WhatHappened);
            }
            if (!empty($assignment->WhatHappendToOthers)) {
                echo "<h3>Vad såg $role->Name hände med andra?</h3>";
                echo nl2br($assignment->WhatHappendToOthers);
            } 
        }
        echo "</div>";
    }
}

?>

<style>
.item {
    border: 1px solid #e0e0e3;
}
</style>

<div class="content">
<h1>Vad hände på <?php echo $current_larp->Name;?></h1>
<a href="#group">Grupper</a><br>
<a href="#main">Huvudkaraktärer</a><br>
<a href="#nonmain">Sidokaraktärer</a><br>
<a href="#npc">NPC'er</a>


<a name="group"></a><h2>Grupper</h2>
<?php 
$groups = Group::getAllRegistered($current_larp);
foreach ($groups as $group) {
    whatHappenedGroup($group);
}
?>
<a name="main"></a><h2>Huvudkaraktärer</h2>
<?php 
$roles = $current_larp->getAllMainRoles(false);
foreach ($roles as $role) {
    whatHappenedRole($role);
}
?>
<a name="nonmain"></a><h2>Sidokaraktärer</h2>
<?php 
$roles = $current_larp->getAllNotMainRoles(false);
foreach ($roles as $role) {
    whatHappenedRole($role);
}

?>
<a name="npc"></a><h2>NPC'er</h2>
<?php 
$roles = Role::getAllNPCToBePlayed($current_larp);
foreach ($roles as $role) {
    whatHappenedRole($role);
}

?>

