<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['VisionId'])) {
    $visionIdArr=$_POST['VisionId'];
    
    $percent=$_POST['percent'];
    
    if (isset($_POST['AbilityId'])) $abilityId = $_POST['AbilityId'];
    
} else {
    header('Location: ../rumour_admin.php');
    exit;
}

foreach ($visionIdArr as $visionId) {
    $vision = Vision::loadById($visionId);

    if (isset($abilityId) && $abilityId != "null") {
        $roles = Role::getAllWithTypeValue($current_larp->Id, "Ability", $abilityId);
    } else {
        $roles = Role::getAllRoles($current_larp);
    }
    
    foreach ($roles as $role) {
        if ($role->isMysLajvare()) continue;

        if (rand(1,100) <= $percent) {
            $vision->addRolesHas(array($role->Id));
        }
    }
        

}




header('Location: ../vision_admin.php');
