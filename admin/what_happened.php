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
        echo "<h2>";
        echo $group->getViewLink();
        echo "</h2>";
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
    }
}



function whatHappenedRole(Role $role) {
    global $current_larp;
    $hasWhatHappened = false;
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    if (!empty($larp_role->WhatHappened) || !empty($larp_role->WhatHappenedToOthers)) $hasWhatHappened = true;
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
        echo "<h2>";
        echo $role->getViewLink();
        $group = $role->getGroup();
        if (isset($group)) echo " - " . $group->getViewLink();
        echo "</h2>";
        foreach($intrigueActors as $intrigueActor) {
            $intrigue = $intrigueActor->getIntrigue();
            echo "<h3><a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a></h3>";
            echo nl2br($intrigueActor->WhatHappened);
        }
        if (!empty($larp_role->WhatHappened)) {
            echo "<h3>Vad hände i övrigt med/för $role->Name?</h3>";
            echo nl2br($larp_role->WhatHappened);
        }
        if (!empty($larp_role->WhatHappenedToOthers)) {
            echo "<h3>Vad såg $role->Name hände med andra?</h3>";
            echo nl2br($larp_role->WhatHappenedToOthers);
        }
    }
}

?>


<div class="content">
<h1>Vad hände på <?php echo $current_larp->Name;?></h1>

<?php 
$groups = Group::getAllRegistered($current_larp);
foreach ($groups as $group) {
    whatHappenedGroup($group);
}

$roles = $current_larp->getAllMainRoles(false);

foreach ($roles as $role) {
    whatHappenedRole($role);
}

$roles = $current_larp->getAllNotMainRoles(false);

foreach ($roles as $role) {
    whatHappenedRole($role);
}

?>

