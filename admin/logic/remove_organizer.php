<?php

require '../header.php';


if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {


    $userId = $_GET['userId'];

    AccessControl::revokeLarp($userId, $current_larp->Id);
}
header('Location: ../settings.php');

