<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';


$groups = Group::all();


foreach ($groups as $group) {
    migrateGruopIntrigueTypes($group);
}


echo "Done";
exit;




function migrateGruopIntrigueTypes(Group $group) {
    echo "$group->Name, $group->Id<br>";
    $larp_groups = getAllRegistrations($group);
    
    $intrigueTypeIds = $group->getSelectedIntrigueTypeIds();
    print_r($intrigueTypeIds);
    echo "<br><br>";
    $group->deleteAllIntrigueTypes();
    foreach ($larp_groups as $larp_group) {
        echo "Lajv: $larp_group->LARPId<br>";
        $larp_group->IntrigueIdeas = $group->IntrigueIdeas;
        $larp_group->update();
        $larp_group->saveAllIntrigueTypes($intrigueTypeIds);
    }
    echo "<br><br>";
}

function getAllRegistrations(Group $group) {
    $sql = "SELECT * FROM regsys_larp_group WHERE GroupId = ? ORDER BY LarpId";
    return LARP_Group::getSeveralObjectsqQuery($sql, array($group->Id));
}
    

