<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $groupId = $_GET['id'];

    $group = Group::loadById($groupId);
    
    
    // Skapa en ny registrering
    $larp_group = LARP_Group::newWithDefault();
    $larp_group->GroupId = $groupId;
    $larp_group->LARPId = $current_larp->Id;
    $larp_group->WantIntrigue = true;
    $larp_group->ApproximateNumberOfMembers = 0;
    $larp_group->NeedFireplace = 0;
    if (HousingRequest::isInUse($current_larp)) $larp_group->HousingRequestId = HousingRequest::allActive($current_larp)[0]->Id;
    $larp_group->create();
    

    header('Location: ../not_registered_roles.php?message=registration_done');
    exit;

}
header('Location: ../index.php');




