<?php
include_once '../header.php';

if (!isset( $_POST['roleId'])) {
    header('Location: ../index.php');
    exit;
}

$role = Role::loadById($_POST['roleId']);


if ($role->isApproved()) $role->unapprove($current_larp);
else $role->approve($current_larp, $current_user);

if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../roles.php');
