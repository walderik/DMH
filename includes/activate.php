<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';

// First we check if the email and code exists...
if (isset($_GET['email'], $_GET['code'])) {
    $user = User::loadByEmail($_GET['email']);
    if (!isset($user) || $user->ActivationCode != $_GET['code']) {
        header('Location: ../index.php?error=activation_not_possible');
        exit;
    }
    $user->ActivationCode = 'activated';
    $user->update();
    header('Location: ../index.php?message=activated');
    exit;
}

header('Location: ../index.php?error=activation_not_possible');