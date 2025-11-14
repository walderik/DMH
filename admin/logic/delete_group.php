<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $group = Group::loadById($_GET['id']);
    }
}


if (!isset($group)) {
    header('Location: ../index.php');
    exit;
}


if ($group->CampaignId != $current_larp->CampaignId) {
    header('Location: ../index.php'); //Tillhör inte den här kampanjen
    exit;
}

if ($group->mayDelete()) {
    Group::delete($group->Id);
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    header('Location: ../index.php?message=group_deleted');
} else {
    header('Location: ../index.php?error=group_cannot_be_deleted');
}

