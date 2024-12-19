<?php
include_once '../header.php';

if (!isset( $_POST['roleId']) && !isset( $_POST['groupId'])) {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['roleId'])) {
    $role = Role::loadById($_POST['roleId']);

    if ($role->isApproved()) $role->unapprove($current_larp, true, $current_person);
    else $role->approve($current_larp, $current_person);
} elseif (isset($_POST['groupId'])) {
    $group = Group::loadById($_POST['groupId']);
    
    if ($group->isApproved()) $group->unapprove($current_larp, true, $current_person);
    else $group->approve($current_larp, $current_person);
}

header('Location: ../approval.php');
