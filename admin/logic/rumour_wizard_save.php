<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rumourIdArr=$_POST['RumourId'];
    
    $groups_roles=$_POST['groups_roles'];
    $main_nonmain=$_POST['main_nonmain'];
    $percent=$_POST['percent'];
    
    if (isset($_POST['LarperTypeId'])) $larpertypeArr = $_POST['LarperTypeId'];
    if (isset($_POST['WealthId'])) $wealthArr = $_POST['WealthId'];
    if (isset($_POST['PlaceOfResidenceId'])) $placeofresidenceArr = $_POST['PlaceOfResidenceId'];
    if (isset($_POST['IntrigueTypeId'])) $intriguetypeArr = $_POST['IntrigueTypeId'];
    
    $age0_6 = false;
    $age7_15 = false;
    $age16_ = false;
    if (isset($_POST['age0-6'])) $age0_6 = true;
    if (isset($_POST['age7-15'])) $age7_15 = true;
    if (isset($_POST['age16-'])) $age16_ = true;
    
} else {
    header('Location: ../rumour_admin.php');
    exit;
}

foreach ($rumourIdArr as $rumourId) {
    $rumour = Rumour::loadById($rumourId);
    $concernedGrops = $rumour->getAllConcernedGroups();
    $concernsArr = $rumour->getConcerns();
    
    if($groups_roles == 'groups' || $groups_roles == 'both') {
        //Fördela till grupper
        $groups = Group::getAllRegistered($current_larp);
        foreach ($groups as $group) {
            if (!$group->isApproved()) continue;
            
            //Kolla så att inte ryktet berör gruppen
            if (in_array($group, $concernedGrops)) continue;
            
            if (isset($wealthArr)) {
                if (!in_array($group->WealthId, $wealthArr)) continue;
            }
            if (isset($placeofresidenceArr)) {
                if (!in_array($group->PlaceOfResidenceId, $placeofresidenceArr)) continue;
            }
            if (isset($intriguetypeArr)) {
                $group_intrigueTypeIds = $group->getSelectedIntrigueTypeIds();
                if (empty(array_intersect($intriguetypeArr, $group_intrigueTypeIds))) continue;
                
            }
            
            //Gruppen matchar kriterierna
            if (rand(1,100) <= $percent) {
                $knows = Rumour_knows::newWithDefault();
                $knows->RumourId = $rumourId;
                $knows->GroupId = $group->Id;
                $knows->create();
            }
        }
    }
    
    if ($groups_roles == 'roles' || $groups_roles == 'both') {
        //Fördela till roller
        if ($main_nonmain == 'main') $roles = Role::getAllMainRoles($current_larp, false);
        elseif ($main_nonmain == 'nonmain') $roles = Role::getAllNotMainRoles($current_larp, false);
        else $roles = Role::getAllRoles($current_larp);
        
        
        foreach ($roles as $role) {
            if ($role->isMysLajvare()) continue;
            if (!$role->isApproved()) continue;
            
            //Kolla så att inte ryktet berör någon  gruppen
            $group = $role->getGroup();
            if (!empty($group) && in_array($group, $concernedGrops)) continue;
            
            //Kolla så att inte ryktet berör den här karaktären
            foreach ($concernsArr as $concerns) {
                if (isset($concerns->RoleId) && ($concerns->RoleId == $role->Id)) continue;
            }
                       
            if (isset($larpertypeArr)) {
                if (!in_array($role->LarperTypeId, $larpertypeArr)) continue;
            }
            if (isset($wealthArr)) {
                if (!in_array($role->WealthId, $wealthArr)) continue;
            }
            if (isset($placeofresidenceArr)) {
                if (!in_array($role->PlaceOfResidenceId, $placeofresidenceArr)) continue;
            }
            if (isset($intriguetypeArr)) {
                if (empty(array_intersect($intriguetypeArr, $role->getSelectedIntrigueTypeIds()))) continue;
                
            }
            $person = $role->getPerson();
            $age = $person->getAgeAtLarp($current_larp);
            if (($age <= 6) && !$age0_6) continue;
            if (($age >= 7) && ($age <= 15) && !$age7_15) continue;
            if (($age >= 16) && !$age16_) continue;
            
            //Rollen matchar kriterierna
            if (rand(1,100) <= $percent) {
                $knows = Rumour_knows::newWithDefault();
                $knows->RumourId = $rumourId;
                $knows->RoleId = $role->Id;
                $knows->create();
            }
        }
        
    }
}




header('Location: ../rumour_admin.php');
