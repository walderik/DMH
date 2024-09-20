<?php

require '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['campaignId']) or !isset($_GET['personId'])) {
        header('Location: ../index.php');
        exit;
    }
    $campaignId = $_GET['campaignId'];
    $personId = $_GET['personId'];

    AccessControl::revokeCampaign($personId, $campaignId);
}
header('Location: ../campaign_admin.php');

