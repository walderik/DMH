<?php

include_once '../header.php';

if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userIds = $_POST['UserId'];
    $larpId = $_POST['LarpId'];
    $larp = LARP::loadById($larpId);
    foreach ($userIds as $userId) {
        $user = User::loadById($userId);
        
        if (!AccessControl::hasAccessLarp($user, $larp)) {
            AccessControl::grantLarp($userId, $larpId);
       }
    }
    
    header('Location: ../settings.php');
    exit;
    
}
header('Location: ../index.php');