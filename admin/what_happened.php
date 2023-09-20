<?php
include_once 'header.php';
include 'navigation.php';

?>


<div class="content">
<h1>Vad hände på <?php echo $current_larp->Name;?></h1>

<?php 
$roles = $current_larp->getAllMainRoles(true);

foreach ($roles as $role) {
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
    
    
        echo "<h2>$role->Name</h2>";
        foreach($intrigueActors as $intrigueActor) {
            $intrigue = $intrigueActor->getIntrigue();
            echo "<h3>$intrigue->Number. $intrigue->Name</h3>";
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

