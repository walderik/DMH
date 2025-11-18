<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';


$roles = Role::all();


foreach ($roles as $role) {
    migrateRoleIntrigueTypes($role);
}


echo "Done";
exit;




function migrateRoleIntrigueTypes(Role $role) {
    echo "$role->Name, $role->Id<br>";
    $larp_roles = getAllRegistrations($role);
    
    $intrigueTypeIds = $role->getSelectedIntrigueTypeIds();
    $intrigueTypeIds = array();
    

    
    print_r($intrigueTypeIds);
    echo "<br><br>";
    $role->deleteAllIntrigueTypes();
    foreach ($larp_roles as $larp_role) {
        echo "Lajv: $larp_role->LARPId<br>";
        $larp_role->IntrigueIdeas = $role->IntrigueIdeas;
        $larp_role->update();
        $larp_role->saveAllIntrigueTypes($intrigueTypeIds);
    }
    echo "<br><br>";
}

function getAllRegistrations(Role $role) {
    $sql = "SELECT * FROM regsys_larp_role WHERE RoleId = ? ORDER BY LarpId";
    return LARP_Role::getSeveralObjectsqQuery($sql, array($role->Id));
}
    

