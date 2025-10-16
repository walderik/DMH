<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $role = Role::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($role)) {
    header('Location: ../index.php');
    exit;
}

if ($role->isPC($current_larp)) {
    header('Location: ../index.php'); //Spelarkaraktär
    exit;
}


if ($role->CampaignId != $current_larp->CampaignId) {
    header('Location: ../index.php'); //Tillhör inte kampanjen
    exit;
}

if ($role->mayDelete()) {
    Role::delete($role->Id);
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    header('Location: ../index.php?message=role_deleted');
    exit;
}

header('Location: ../index.php'); 

