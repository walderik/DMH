<?php

require '../header.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['campaignId']) or !isset($_GET['userId'])) {
        header('Location: ../index.php');
        exit;
    }
    $campaignId = $_GET['campaignId'];
    $userId = $_GET['userId'];

    $access = AccessControl::loadByIds($userId, $campaignId);
    if (!empty($access)) {
        AccessControl::delete($access->Id);
    }
    
}
header('Location: ../campaign_admin.php');

