<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userIds = $_POST['UserId'];
    $campaignId = $_POST['CampaignId'];
    $campaign = Campaign::loadById($campaignId);
    foreach ($userIds as $userId) {
       
       AccessControl::grantCampaign($userId, $campaignId);
       }
    }
    
    header('Location: ../campaign_admin.php');
    exit;
    
}
header('Location: ../index.php');