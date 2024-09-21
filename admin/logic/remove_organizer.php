<?php

require '../header.php';


if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {


    $personId = $_GET['personId'];

    AccessControl::revokeLarp($personId, $current_larp->Id);
}
header('Location: ../settings.php');

