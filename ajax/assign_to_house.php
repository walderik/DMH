<?php
include_once 'header.php';

// get the parameters from URL
if (isset($_REQUEST["roleId"])) $roleId = $_REQUEST["roleId"];
if (isset($_REQUEST["groupId"])) $groupId = $_REQUEST["groupId"];
if (isset($_REQUEST["fromGroupId"])) $fromGroupId = $_REQUEST["fromGroupId"];
if (isset($_REQUEST["fromHouseId"])) $fromHouseId = $_REQUEST["fromHouseId"];
if (isset($_REQUEST["toHouseId"])) $toHouseId = $_REQUEST["toHouseId"];

if (isset($roleId)) {
    
} elseif (isset($groupId)) {
    $group = Group::loadById($groupId);
    if (isset($fromHouseId)) {
        $fromHouse = House::loadById($fromHouseId);
        $group_members = Person::getGroupMembersInHouse($group, $fromHouse, $current_larp);
    } else {
        $group_members = Person::getPersonsInGroupWithoutHousing($group, $current_larp);
    }

    if (isset($toHouseId)) {
        foreach ($group_members as $person) {
            assign($person, $toHouseId, $current_larp);
        }
    } else {
        foreach ($group_members as $person) {
            unassign($person, $current_larp);
        }
        
    }
    $res = array();
    $res[0] = "";
    $res[1] = "";
    if (isset($fromHouseId)) {
        $fromHouse = House::loadById($fromHouseId);
        $personsInFromHouse = Person::personsAssignedToHouse($fromHouse, $current_larp);  
        $res[0] = count($personsInFromHouse);
    }
    if (isset($toHouseId)) {
        $toHouse = House::loadById($toHouseId);
        $personsInToHouse = Person::personsAssignedToHouse($toHouse, $current_larp);
        $res[1] = count($personsInToHouse);
    }
    echo implode(";",$res);   
}  else {  
    header('Location: ../admin/index.php');
    exit;
}



function assign(Person $person, $houseId, LARP $larp) {
    //Ta bort gammalt boende
    Housing::deleteHousing($larp->Id, $person->Id);


    $housing = Housing::newWithDefault();
    $housing->HouseId = $houseId;
    $housing->PersonId = $person->Id;
    $housing->LARPId = $larp->Id;
    $housing->create();
   
}

function unassign(Person $person, LARP $larp) {
    //Ta bort gammalt boende
    Housing::deleteHousing($larp->Id, $person->Id);
}
