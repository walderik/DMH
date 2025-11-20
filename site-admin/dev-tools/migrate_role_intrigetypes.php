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
    print_r($intrigueTypeIds);
    echo "<br><br>";
    $role->deleteAllIntrigueTypes();
    echo "Radera gamla intrigtyper<br>";
    foreach ($larp_roles as $larp_role) {
        echo "Lajv: $larp_role->LARPId<br>";
        $larp_role->IntrigueIdeas = $role->IntrigueSuggestions;
        $larp_role->update();
        if (!empty($intrigueTypeIds)) {
            $larp_role->saveAllIntrigueTypes($intrigueTypeIds);
            print_r($larp_role->getSelectedIntrigueTypeIds());
        }
        echo "<br>";

    }
    echo "<br><br>";
}

function getAllRegistrations(Role $role) {
    $sql = "SELECT * FROM regsys_larp_role WHERE RoleId = ? ORDER BY LarpId";
    return LARP_Role::getSeveralObjectsqQuery($sql, array($role->Id));
}
    

