<?php
include_once '../header.php';

if (!isset( $_POST['roleId'])) {
    header('Location: ../index.php');
    exit;
}

$role = Role::loadById($_POST['roleId']);
if (empty($role)) {
    header('Location: ../index.php');
    exit;
}


if ($role->UserMayEdit == 0) {
    $role->UserMayEdit = 1;

}
else {
    $role->UserMayEdit = 0;
}

$role->update();


if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../roles.php');
