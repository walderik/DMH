<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset( $_GET['user_id'])) {
    header('Location: ../../admin/user_admin.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = User::loadById($_GET['user_id']);
    if (is_null($user)) {
        header('Location: ../../participant/index.php');
        exit;
    }
    $user->ActivationCode = 'activated';
    $user->update();
    header('Location: ../../admin/user_admin.php');
    exit;
} else  {
    header('Location: ../../participant/index.php');
    exit;
}

header('Location: ../../index.php');
