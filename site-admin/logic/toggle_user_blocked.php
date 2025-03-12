<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset( $_GET['user_id'])) {
    header('Location: ../../site-admin/user_admin.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = User::loadById($_GET['user_id']);
    if (is_null($user)) {
        header('Location: ../../participant/index.php');
        exit;
    }
    if ($user->Blocked == 0) {
        $user->Blocked = 1;
    }
    else {
        $user->Blocked = 0;
        
    }

    $user->update();
    header('Location: ../../site-admin/user_admin.php');
    exit;
} else  {
    header('Location: ../../participant/index.php');
    exit;
}

header('Location: ../../index.php');
